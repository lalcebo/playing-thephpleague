<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Application;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

trait RequestsTest
{
    /**
     * Create a server request.
     *
     * @param  string  $method  The HTTP method
     * @param  string|UriInterface  $uri  The URI
     * @param  array  $serverParams  The server parameters
     * @return ServerRequestInterface The request
     */
    protected function createRequest(string $method, UriInterface|string $uri, array $serverParams = []): ServerRequestInterface
    {
        return Application::getInstance()->getContainer()->get(ServerRequestFactoryInterface::class)->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a form request.
     *
     * @param  string  $method  The HTTP method
     * @param  string|UriInterface  $uri  The URI
     * @param  array|null  $data  The form data
     * @return ServerRequestInterface The request
     */
    protected function createFormRequest(string $method, $uri, ?array $data = null): ServerRequestInterface
    {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    /**
     * Create a new response.
     *
     * @param  int  $code  HTTP status code; defaults to 200
     * @param  string  $reasonPhrase  Reason phrase to associate with status code
     * @return ResponseInterface The response
     */
    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return Application::getInstance()->getContainer()->get(ResponseFactoryInterface::class)->createResponse($code, $reasonPhrase);
    }

    /**
     * Assert that the response body contains a string.
     *
     * @param  ResponseInterface  $response  The response
     * @param  string  $needle  The expected string
     */
    protected function assertResponseContains(ResponseInterface $response, string $needle): void
    {
        $body = (string) $response->getBody();

        $this->assertStringContainsString($needle, $body);
    }
}
