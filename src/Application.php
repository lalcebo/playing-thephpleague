<?php

declare(strict_types=1);

namespace Lalcebo\League;

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
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nette\Schema\Expect;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Whoops\Exception\Inspector;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class Application
{
    private static Container $container;

    public function __construct(protected Container $object)
    {
        self::$container = $object;
        self::$container->add(ServerRequestInterface::class, ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES));

        $this->environment();
        $this->configuration();
        $this->logger();
        $this->whoops();
        $this->events();
        $this->route();

        // sets the default timezone used
        date_default_timezone_set(self::$container->get('config')->get('app.timezone'));
    }

    public static function getContainer(): Container
    {
        if (self::$container === null) {
            self::$container = self::buildContainer();
        }

        return self::$container;
    }

    public static function make(): Application
    {
        return new self(self::buildContainer());
    }

    public function register(string $provider): void
    {
        self::$container->addServiceProvider(new $provider());
    }

    public function getRoute(): Router
    {
        return self::getContainer()->get('router');
    }

    public function run(): void
    {
        // send the response to the browser
        (new SapiEmitter())
            ->emit($this->getRoute()->dispatch(self::getContainer()->get(ServerRequestInterface::class)));
    }

    private static function buildContainer(): Container
    {
        return (new Container())
            ->delegate(new ReflectionContainer());
    }

    private function environment(): void
    {
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(ServerConstAdapter::class)
            ->addAdapter(EnvConstAdapter::class)
            ->addWriter(PutenvAdapter::class)
            ->immutable()
            ->make();

        Dotenv::create($repository, __DIR__ . '/../')->load();

        self::$container->add(AdapterRepository::class, fn () => $repository);
        self::$container->add('env', fn () => $repository);
    }

    private function configuration(): void
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

        self::$container->add(Configuration::class, fn () => $config);
        self::$container->add('config', fn () => $config);
    }

    private function logger(): void
    {
        $level = self::$container->get('config')->get('app.debug') ? Level::Debug : Level::Error;

        $logger = new Logger('local');
        $logger->pushHandler(new StreamHandler('php://stdout', $level));

        self::$container->add(Logger::class, fn () => $logger);
        self::$container->add('logger', fn () => $logger);
    }

    private function events(): void
    {
        $event = new EventDispatcher(new PrioritizedListenerRegistry());

        self::$container->add(EventDispatcher::class, fn () => $event);
        self::$container->add('event', fn () => $event);
    }

    private function whoops(): void
    {
        $isJson = self::$container->get(ServerRequestInterface::class)->getHeaderLine('Content-Type') === 'application/json';
        $browserHandler = $isJson ? new JsonResponseHandler() : new PrettyPageHandler();
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        $consoleHandler = class_exists(\NunoMaduro\Collision\Handler::class) ? new \NunoMaduro\Collision\Handler() : new PlainTextHandler();

        $run = new Run();
        $run->pushHandler(runningInConsole() ? $consoleHandler : $browserHandler);
        $run->pushHandler(fn (Throwable $e, Inspector $inspector, Run $run) => self::$container->get('logger')->error($e->getMessage(), $e->getTrace()));
        $run->register();
    }

    private function route(): void
    {
        /** @var ApplicationStrategy $strategy */
        $strategy = (new ApplicationStrategy())->setContainer(self::$container);
        $router = (new Router())->setStrategy($strategy);

        self::$container->add(Router::class, fn () => $router);
        self::$container->add('router', fn () => $router);
    }
}
