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
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

include __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

// init
$reflection = new ReflectionContainer();
$config = new Configuration();
$event = new EventDispatcher(new PrioritizedListenerRegistry());
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

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

$router->group('', function () use ($router) {
    require __DIR__ . '/routes.php';
});

return $router->dispatch($request);
