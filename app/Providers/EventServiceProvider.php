<?php

namespace App\Providers;

use App\Events\LoginUserEvent;
use App\Listeners\LoginUserListener;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Event\EventDispatcher;
use Throwable;

class EventServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<class-string>>
     */
    protected array $listen = [
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
                $event->subscribeTo($eventName, new $listener);
            }
        }
    }
}