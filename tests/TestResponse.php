<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface as Response;
use Tests\Traits\Http\ResponseJsonAsserts;

/**
 * @mixin Response
 */
final class TestResponse
{
    use ResponseJsonAsserts;

    /**
     * The response to delegate to.
     */
    public Response $baseResponse;

    /**
     * Create a new test response instance.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->baseResponse = $response;
    }

    /**
     * Dynamically access base response parameters.
     */
    public function __get(string $key): mixed
    {
        /* @phpstan-ignore-next-line */
        return $this->baseResponse->{$key};
    }

    /**
     * Proxy isset() checks to the underlying base response.
     *
     * @return bool
     */
    public function __isset(string $key)
    {
        /* @phpstan-ignore-next-line */
        return isset($this->baseResponse->{$key});
    }

    /**
     * Handle dynamic calls into macros or pass missing methods to the base response.
     *
     * @param  array<string,mixed>  $args
     */
    public function __call(string $method, array $args): mixed
    {
        /* @phpstan-ignore-next-line */
        return $this->baseResponse->{$method}(...$args);
    }

    /**
     * Create a new TestResponse from another response.
     *
     * @return static
     */
    public static function fromBaseResponse(Response $response): self
    {
        return new self($response);
    }

    /**
     * Assert that the response has a 200 status code.
     */
    public function assertOk(): self
    {
        return $this->assertStatus(200);
    }

    /**
     * Assert that the response has a 201 status code.
     */
    public function assertCreated(): self
    {
        return $this->assertStatus(201);
    }

    /**
     * Assert that the response has the given status code and no content.
     */
    public function assertNoContent(int $status = 204): self
    {
        $this->assertStatus($status);

        Assert::assertEmpty((string) $this->getBody(), 'Response content is not empty.');

        return $this;
    }

    /**
     * Assert that the response has a not found status code.
     */
    public function assertNotFound(): self
    {
        return $this->assertStatus(404);
    }

    /**
     * Assert that the response has a forbidden status code.
     */
    public function assertForbidden(): self
    {
        return $this->assertStatus(403);
    }

    /**
     * Assert that the response has an unauthorized status code.
     */
    public function assertUnauthorized(): self
    {
        return $this->assertStatus(401);
    }

    /**
     * Assert that the response has a 422 Unprocessable status code.
     */
    public function assertUnprocessable(): self
    {
        return $this->assertStatus(422);
    }

    /**
     * Asserts that the response has a 400 Bad Request status code.
     */
    public function assertBadRequest(): self
    {
        return $this->assertStatus(400);
    }

    /**
     * Asserts that the response has a 405 Method Not Allowed status code.
     */
    public function assertMethodNotAllowed(): self
    {
        return $this->assertStatus(405);
    }

    /**
     * Asserts that the response has a 410 Gone status code.
     */
    public function assertGone(): self
    {
        return $this->assertStatus(410);
    }

    /**
     * Asserts that the response has a 500 Internal Server Error status code.
     */
    public function assertInternalServerError(): self
    {
        return $this->assertStatus(500);
    }

    /**
     * Asserts that the response has a 501 Not Implemented status code.
     */
    public function assertNotImplemented(): self
    {
        return $this->assertStatus(501);
    }

    /**
     * Assert that the response has the given status code.
     */
    public function assertStatus(int $status): self
    {
        Assert::assertEquals($status, $this->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response contains the given header and equals the optional value.
     */
    public function assertHeader(string $headerName, string $value = ''): self
    {
        Assert::assertTrue(
            $this->hasHeader($headerName), "Header [$headerName] not present on response."
        );

        $actual = $this->getHeaderLine($headerName);

        if ($value !== '') {
            Assert::assertEquals(
                $value, $this->getHeaderLine($headerName),
                "Header [$headerName] was found, but value [$actual] does not match [$value]."
            );
        }

        return $this;
    }

    /**
     * Asserts that the response does not contain the given header.
     */
    public function assertHeaderMissing(string $headerName): self
    {
        Assert::assertFalse(
            $this->hasHeader($headerName), "Unexpected header [$headerName] is present on response."
        );

        return $this;
    }

    /**
     * Assert that the given string is contained within the response.
     */
    public function assertSee(string $value): self
    {
        Assert::assertStringContainsString($value, (string) $this->getBody());

        return $this;
    }

    /**
     * Assert that the given string is not contained within the response.
     */
    public function assertDontSee(string $value): self
    {
        Assert::assertStringNotContainsString($value, (string) $this->getBody());

        return $this;
    }
}
