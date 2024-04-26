<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WelcomeController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'title'   => 'My New Simple API',
            'version' => 1,
        ]);
    }
}