<?php

declare(strict_types=1);

/** @var League\Route\Router $router */

use App\Http\Controllers\WelcomeController;

$router->get('/', WelcomeController::class);
