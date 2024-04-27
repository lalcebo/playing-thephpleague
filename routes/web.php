<?php

declare(strict_types=1);

/** @var League\Route\RouteGroup $router */

use App\Http\Controllers\WelcomeController;

$router->get('/', WelcomeController::class);
