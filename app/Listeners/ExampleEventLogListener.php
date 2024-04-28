<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExampleEvent;

final class ExampleEventLogListener extends Listener
{
    public function __invoke(object $event): void
    {
        /** @var ExampleEvent $event */
        info('Example Event Log', $event->data);
    }
}
