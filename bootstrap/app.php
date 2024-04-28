<?php

declare(strict_types=1);

/** @var Router $router */
/** @var ApplicationStrategy $strategy */

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\AdapterRepository;
use Dotenv\Repository\RepositoryBuilder;
use Laminas\Diactoros\ServerRequestFactory;
use League\Config\Configuration;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Event\EventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nette\Schema\Expect;
use Whoops\Exception\Inspector;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

include __DIR__ . '/../vendor/autoload.php';

// container
$reflection = new ReflectionContainer();
$container = new Container();
$container->delegate($reflection);

// environment vars
$repository = RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(ServerConstAdapter::class)
    ->addAdapter(EnvConstAdapter::class)
    ->addWriter(PutenvAdapter::class)
    ->immutable()
    ->make();
(Dotenv\Dotenv::create($repository, __DIR__ . '/../'))->load();
$container->add(AdapterRepository::class, fn () => $repository)->setAlias('env');

// config
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
$container->add(Configuration::class, fn () => $config);
$container->add('config', fn () => $config);

// sets the default timezone used
date_default_timezone_set($config->get('app.timezone'));

// logger
$logger = new Logger('local');
$logger->pushHandler(new StreamHandler('php://stdout', $config->get('app.debug') ? Level::Debug : Level::Error));
$container->add(Logger::class, fn () => $logger);
$container->add('logger', fn () => $logger);

// request
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

// whoops
$browserHandler = $request->getHeaderLine('Content-Type') === 'application/json' ? new JsonResponseHandler() : new PrettyPageHandler();
$consoleHandler = class_exists(NunoMaduro\Collision\Handler::class) ? new NunoMaduro\Collision\Handler() : new PlainTextHandler();

$run = new Run();
$run->pushHandler(runningInConsole() ? $consoleHandler : $browserHandler);
$run->pushHandler(fn (Throwable $e, Inspector $inspector, Run $run) => $logger->error($e->getMessage(), $e->getTrace()));
$run->register();

// events
$event = new EventDispatcher(new PrioritizedListenerRegistry());
$container->add(EventDispatcher::class, fn () => $event);
$container->add('event', fn () => $event);

// providers
$container->addServiceProvider(new AppServiceProvider());
$container->addServiceProvider(new EventServiceProvider());

$strategy = (new ApplicationStrategy())->setContainer($container);
$router = (new Router())->setStrategy($strategy);

$router->group('', fn (RouteGroup $router) => require __DIR__ . '/../routes/web.php');
$router->group('/api', fn (RouteGroup $router) => require __DIR__ . '/../routes/api.php');

return $router->dispatch($request);
