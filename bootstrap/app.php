<?php

declare(strict_types=1);

use App\Application;
use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RouteServiceProvider;

include __DIR__ . '/../vendor/autoload.php';

$app = Application::getInstance()
    ->request()
    ->environment()
    ->configuration()
    ->logger()
    ->whoops()
    ->events()
    ->route();

// sets the default timezone used
date_default_timezone_set($app->getContainer()->get('config')->get('app.timezone'));

$app->register(AppServiceProvider::class);
$app->register(EventServiceProvider::class);
$app->register(RouteServiceProvider::class);

return $app;
