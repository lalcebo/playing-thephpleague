<?php

declare(strict_types=1);

use App\Application;

test('example', function () {
    $request = $this->createRequest('GET', '/');

    /** @var Laminas\Diactoros\Response\JsonResponse $response */
    $response = Application::getInstance()->getContainer()->get('router')->dispatch($request);

    expect($response->getStatusCode())
        ->toEqual(200)
        ->and($response->getPayload())
        ->toEqual([
            'name' => config('app.name'),
            'title' => 'My New Simple API',
            'version' => 1,
        ]);
});
