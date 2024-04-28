<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExampleEvent;

final class ExampleEventUserNotifyListener extends Listener
{
    public function __invoke(object $event): void
    {
        /** @var ExampleEvent $event */
        info('Example Event User Notify', $event->data);
    }
}
