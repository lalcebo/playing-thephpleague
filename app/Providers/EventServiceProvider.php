<?php

declare(strict_types=1);

namespace App\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Event\EventDispatcher;
use Throwable;

final class EventServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<class-string>>
     */
    private array $listen = [
        //
    ];

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
        /** @var EventDispatcher $event */
        $event = $this->getContainer()->get(EventDispatcher::class);

        foreach ($this->listen as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $event->subscribeTo($eventName, new $listener());
            }
        }
    }
}
