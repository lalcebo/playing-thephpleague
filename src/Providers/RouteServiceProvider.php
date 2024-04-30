<?php

declare(strict_types=1);

namespace App\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\RouteGroup;
use League\Route\Router;
use Throwable;

final class RouteServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {
        //
    }

    /**
     * @throws Throwable
     */
    public function boot(): void
    {
        /** @var Router $route */
        $route = $this->getContainer()->get(Router::class);

        $route->group('', fn (RouteGroup $router) => require __DIR__ . '/../../routes/web.php');
        $route->group('/api', fn (RouteGroup $router) => require __DIR__ . '/../../routes/api.php');
    }
}
