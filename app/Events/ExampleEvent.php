<?php

declare(strict_types=1);

namespace App\Events;

final class ExampleEvent
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(public array $data = [])
    {
    }
}
