<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RouteServiceProvider;
use Lalcebo\League\Application;

include __DIR__ . '/../vendor/autoload.php';

$app = Application::make();
$app->register(AppServiceProvider::class);
$app->register(EventServiceProvider::class);
$app->register(RouteServiceProvider::class);

return $app;
