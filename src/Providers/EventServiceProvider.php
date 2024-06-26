<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ExampleEvent;
use App\Listeners\ExampleEventLogListener;
use App\Listeners\ExampleEventUserNotifyListener;
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
        ExampleEvent::class => [
            ExampleEventLogListener::class,
            ExampleEventUserNotifyListener::class,
        ],
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

        foreach ($this->listen as $eventName => $eventListeners) {
            foreach ($eventListeners as $eventListener) {
                /** @var callable $listener */
                $listener = new $eventListener;

                $event->subscribeTo($eventName, $listener);
            }
        }
    }
}
