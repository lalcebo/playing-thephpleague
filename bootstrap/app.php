<?php

declare(strict_types=1);

/** @var Router $router */
/** @var ApplicationStrategy $strategy */

use App\Providers\AppServiceProvider;
use Laminas\Diactoros\ServerRequestFactory;
use League\Config\Configuration;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

include __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

$container = new Container();
$container->delegate(new League\Container\ReflectionContainer());
$container->addServiceProvider(new AppServiceProvider());
$container->add(Configuration::class, fn () => new Configuration());

$strategy = (new League\Route\Strategy\ApplicationStrategy)->setContainer($container);
$router = (new League\Route\Router)->setStrategy($strategy);

$router->group('', function () use ($router) {
    require __DIR__ . '/routes.php';
});

return $router->dispatch(ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES));
