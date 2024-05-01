<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Traits;

use BadMethodCallException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tests\Traits\Http\Headers;
use Tests\Traits\Http\Methods;
use UnexpectedValueException;

trait Application
{
    use Headers;
    use Methods;

    protected ContainerInterface $container;

    /**
     * Setup DI container.
     *
     * TestCases must call this method inside setUp().
     *
     * @param  ContainerInterface|null  $container  The container
     *
     * @throws UnexpectedValueException
     */
    protected function setUpContainer(?ContainerInterface $container = null): void
    {
        if ($container instanceof ContainerInterface) {
            $this->container = $container;

            return;
        }

        throw new UnexpectedValueException('Container must be initialized');
    }

    /**
     * Read array value with dot notation.
     */
    protected function getArrayValue(array $data, string $path, $default = null)
    {
        $currentValue = $data;
        $keyPaths = explode('.', $path);

        foreach ($keyPaths as $currentKey) {
            if (isset($currentValue->$currentKey)) {
                $currentValue = $currentValue->$currentKey;
                continue;
            }

            if (isset($currentValue[$currentKey])) {
                $currentValue = $currentValue[$currentKey];
                continue;
            }

            return $default;
        }

        return $currentValue ?? $default;
    }

    /**
     * Define an object or a value in the container.
     */
    protected function setContainerValue(string $name, mixed $value): void
    {
        if (method_exists($this->container, 'add')) {
            $this->container->add($name, $value);

            return;
        }

        throw new BadMethodCallException('This DI container does not support this feature');
    }

    /**
     * Create a server request.
     */
    protected function createRequest(string $method, UriInterface|string $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->container->get(ServerRequestFactoryInterface::class)->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a form request.
     */
    protected function createFormRequest(string $method, UriInterface|string $uri, ?array $data = null): ServerRequestInterface
    {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    /**
     * Create a new response.
     */
    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->container->get(ResponseFactoryInterface::class)->createResponse($code, $reasonPhrase);
    }

    /**
     * Assert that the response body contains a string.
     */
    protected function assertResponseContains(ResponseInterface $response, string $needle): void
    {
        $body = (string) $response->getBody();

        $this->assertStringContainsString($needle, $body);
    }

    /**
     * Create a JSON request.
     */
    protected function createJsonRequest(string $method, UriInterface|string $uri, ?array $data = null): ServerRequestInterface
    {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request->getBody()->write((string) json_encode($data, JSON_THROW_ON_ERROR));
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Verify that the specified array is an exact match for the returned JSON.
     */
    protected function assertJsonData(array $expected, ResponseInterface $response): void
    {
        $data = $this->getJsonData($response);

        $this->assertSame($expected, $data);
    }

    /**
     * Get JSON response as an array.
     */
    protected function getJsonData(ResponseInterface $response): array
    {
        $actual = (string) $response->getBody();
        $this->assertJson($actual);

        return (array) json_decode($actual, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Verify JSON response.
     */
    protected function assertJsonContentType(ResponseInterface $response): void
    {
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }

    /**
     * Verify that the specified array is an exact match for the returned JSON.
     */
    protected function assertJsonValue(mixed $expected, string $path, ResponseInterface $response): void
    {
        $this->assertSame($expected, $this->getArrayValue($this->getJsonData($response), $path));
    }
}
