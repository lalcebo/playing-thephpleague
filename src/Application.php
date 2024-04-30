<?php

declare(strict_types=1);

namespace App;

use DirectoryIterator;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\AdapterRepository;
use Dotenv\Repository\RepositoryBuilder;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Config\Configuration;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Event\EventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\StrategyAwareInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nette\Schema\Expect;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Whoops\Exception\Inspector;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class Application
{
    private static Application $instance;

    private static Container $container;

    private function __construct(Container $container)
    {
        $this->setContainer($container);

        $this->getContainer()->add(ServerRequestFactoryInterface::class, new ServerRequestFactory());
        $this->getContainer()->add(ServerRequestInterface::class, ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES));
    }

    public static function getInstance(): Application
    {
        if (! isset(self::$instance)) {
            self::$instance = new self((new Container())->delegate(new ReflectionContainer()));
        }

        return self::$instance;
    }

    public function setContainer(Container $container): Application
    {
        self::$container = $container;

        return $this;
    }

    public function getContainer(): Container
    {
        return self::$container;
    }

    public function register(string $provider): void
    {
        self::$container->addServiceProvider(new $provider());
    }

    public function getRoute(): Router
    {
        return self::$container->get('router');
    }

    public function run(): void
    {
        // send the response to the browser
        (new SapiEmitter())
            ->emit(self::$container->get('router')->dispatch(self::$container->get(ServerRequestInterface::class)));
    }

    public function environment(): Application
    {
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(ServerConstAdapter::class)
            ->addAdapter(EnvConstAdapter::class)
            ->addWriter(PutenvAdapter::class)
            ->immutable()
            ->make();

        Dotenv::create($repository, __DIR__ . '/playing-thephpleague/')->load();

        self::$container->add(AdapterRepository::class, fn () => $repository);
        self::$container->add('env', fn () => $repository);

        return $this;
    }

    public function configuration(): Application
    {
        $config = new Configuration();
        $schema = Expect::array();
        $configDirectory = new DirectoryIterator(__DIR__ . '/../config');

        foreach ($configDirectory as $file) {
            /** @var DirectoryIterator $file */
            if ($file->isFile() && $file->isReadable() && $file->getExtension() === 'php') {
                $section = mb_strtolower($file->getBasename('.php'));
                $config->addSchema($section, $schema);
                $config->merge([$section => include $file->getPathname()]);
            }
        }

        self::$container->add(Configuration::class, fn (): \League\Config\Configuration => $config);
        self::$container->add('config', fn (): \League\Config\Configuration => $config);

        return $this;
    }

    public function logger(): Application
    {
        $level = self::$container->get('config')->get('app.debug') ? Level::Debug : Level::Error;

        $logger = new Logger('local');
        $logger->pushHandler(new StreamHandler('php://stdout', $level));

        self::$container->add(Logger::class, fn (): \Monolog\Logger => $logger);
        self::$container->add('logger', fn (): \Monolog\Logger => $logger);

        return $this;
    }

    public function events(): Application
    {
        $event = new EventDispatcher(new PrioritizedListenerRegistry());

        self::$container->add(EventDispatcher::class, fn (): \League\Event\EventDispatcher => $event);
        self::$container->add('event', fn (): \League\Event\EventDispatcher => $event);

        return $this;
    }

    public function whoops(): Application
    {
        $isJson = self::$container->get(ServerRequestInterface::class)->getHeaderLine('Content-Type') === 'application/json';
        $browserHandler = $isJson ? new JsonResponseHandler() : new PrettyPageHandler();
        $consoleHandler = class_exists(\NunoMaduro\Collision\Handler::class) ? new \NunoMaduro\Collision\Handler() : new PlainTextHandler();

        $run = new Run();
        $run->pushHandler(runningInConsole() ? $consoleHandler : $browserHandler);
        $run->pushHandler(fn (Throwable $e, Inspector $inspector, Run $run) => self::$container->get('logger')->error($e->getMessage(), $e->getTrace()));
        $run->register();

        return $this;
    }

    public function route(): Application
    {
        /** @var ApplicationStrategy $strategy */
        $strategy = (new ApplicationStrategy())->setContainer(self::$container);
        $router = (new Router())->setStrategy($strategy);

        self::$container->add(Router::class, fn (): StrategyAwareInterface => $router);
        self::$container->add('router', fn (): StrategyAwareInterface => $router);

        return $this;
    }
}
