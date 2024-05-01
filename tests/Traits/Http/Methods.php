<?php

declare(strict_types=1);

namespace Tests\Traits\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestResponse;

trait Methods
{
    /**
     * Visit the given URI with a GET request.
     *
     * @param  array<string, array<string>|string>  $headers
     */
    final public function get(string $uri, array $headers = []): TestResponse
    {
        $request = $this->createRequest('GET', $uri);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param  array<string, array<string>|string>  $headers
     */
    final public function getJson(string $uri, array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('GET', $uri);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createFormRequest('POST', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function postJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('POST', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function put(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createFormRequest('PUT', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a PUT request, expecting a JSON response.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function putJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('PUT', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function patch(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createFormRequest('PATCH', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a PATCH request, expecting a JSON response.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function patchJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('PATCH', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function delete(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createFormRequest('DELETE', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with a DELETE request, expecting a JSON response.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function deleteJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('DELETE', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with an OPTIONS request.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function options(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createFormRequest('OPTIONS', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * Visit the given URI with an OPTIONS request, expecting a JSON response.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string>|string>  $headers
     */
    final public function optionsJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        $request = $this->createJsonRequest('OPTIONS', $uri, $data);

        return $this->send($request, $headers);
    }

    /**
     * @param  array<string, array<string>|string>  $headers
     */
    private function send(MessageInterface|ServerRequestInterface $request, array $headers): TestResponse
    {
        if (property_exists(static::class, 'defaultHeaders')) {
            $headers = array_merge($this->defaultHeaders, $headers);
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        /* @phpstan-ignore-next-line */
        return new TestResponse($this->app->handle($request));
    }
}
