<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\LoginUserEvent;
use Laminas\Diactoros\Response\JsonResponse;
use League\Event\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WelcomeController
{
    public function __construct(protected EventDispatcher $eventDispatcher)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->eventDispatcher->dispatch(new LoginUserEvent(1, 'secret'));

        return new JsonResponse([
            'title'   => 'My New Simple API',
            'version' => 1,
        ]);
    }
}