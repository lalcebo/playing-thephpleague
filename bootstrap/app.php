<?php

declare(strict_types=1);

/** @var Router $router */
/** @var ApplicationStrategy $strategy */

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
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
use Whoops\Exception\Inspector;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

include __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

// environment vars
foreach ((Dotenv\Dotenv::createImmutable(__DIR__ . '/../'))->load() as $env => $value) {
    putenv("$env=$value");
}

// logger
$log = new Logger('local');
$level = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN) ? Level::Debug : Level::Error;
$log->pushHandler(new StreamHandler('php://stdout', $level));

// init
$reflection = new ReflectionContainer();
$config = new Configuration();
$event = new EventDispatcher(new PrioritizedListenerRegistry());
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

// whoops
$browserHandler = $request->getHeaderLine('Content-Type') === 'application/json' ? new JsonResponseHandler() : new PrettyPageHandler();
$consoleHandler = class_exists(NunoMaduro\Collision\Handler::class) ? new NunoMaduro\Collision\Handler() : new PlainTextHandler();

$run = new Run();
$run->pushHandler(PHP_SAPI === 'cli' ? $consoleHandler : $browserHandler);
$run->pushHandler(fn (Throwable $e, Inspector $inspector, Run $run) => $log->error($e->getMessage(), $e->getTrace()));
$run->register();

// container
$container = new Container();
$container->delegate($reflection);

// binding
$container->add(Configuration::class, fn () => $config);
$container->add(EventDispatcher::class, fn () => $event);

// providers
$container->addServiceProvider(new AppServiceProvider());
$container->addServiceProvider(new EventServiceProvider());

$strategy = (new ApplicationStrategy())->setContainer($container);
$router = (new Router())->setStrategy($strategy);

$router->group('', fn (RouteGroup $router) => require __DIR__ . '/../routes/web.php');
$router->group('/api', fn (RouteGroup $router) => require __DIR__ . '/../routes/api.php');

return $router->dispatch($request);
