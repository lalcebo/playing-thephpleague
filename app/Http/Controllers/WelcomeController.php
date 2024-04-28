<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\ExampleEvent;
use Laminas\Diactoros\Response\JsonResponse;
use League\Event\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WelcomeController
{
    public function __construct(protected EventDispatcher $eventDispatcher)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        event(new ExampleEvent(['id' => 'exampleEventId']));

        return new JsonResponse([
            'name' => config('app.name'),
            'title' => 'My New Simple API',
            'version' => 1,
        ]);
    }
}
