<?php

declare(strict_types=1);

use Tests\TestCase;
use Tests\TestResponse;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Define additional headers to be sent with the request.
 *
 * @param  array<string, string>  $headers
 */
function withHeaders(array $headers): TestCase
{
    return test()->withHeaders(...func_get_args());
}

/**
 * Add a header to be sent with the request.
 */
function withHeader(string $name, string $value): TestCase
{
    return test()->withHeader(...func_get_args());
}

/**
 * Add an authorization token for the request.
 */
function withToken(string $token, string $type = 'Bearer'): TestCase
{
    return test()->withToken(...func_get_args());
}

/**
 * Set the request authentication credentials using Basic Authentication.
 */
function withBasicAuth(string $username, string $password): TestCase
{
    return test()->withBasicAuth(...func_get_args());
}

/**
 * Flush all the configured headers.
 */
function flushHeaders(): TestCase
{
    return test()->flushHeaders(...func_get_args());
}

/**
 * Visit the given URI with a GET request.
 *
 * @param  array<string, string>  $headers
 */
function get(string $uri, array $headers = []): TestResponse
{
    return test()->get(...func_get_args());
}

/**
 * Visit the given URI with a GET request, expecting a JSON response.
 *
 * @param  array<string, string>  $headers
 */
function getJson(string $uri, array $headers = []): TestResponse
{
    return test()->getJson(...func_get_args());
}

/**
 * Visit the given URI with a POST request.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function post(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->post(...func_get_args());
}

/**
 * Visit the given URI with a POST request, expecting a JSON response.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function postJson(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->postJson(...func_get_args());
}

/**
 * Visit the given URI with a PUT request.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function put(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->put(...func_get_args());
}

/**
 * Visit the given URI with a PUT request, expecting a JSON response.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function putJson(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->putJson(...func_get_args());
}

/**
 * Visit the given URI with a PATCH request.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function patch(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->patch(...func_get_args());
}

/**
 * Visit the given URI with a PATCH request, expecting a JSON response.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function patchJson(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->patchJson(...func_get_args());
}

/**
 * Visit the given URI with a DELETE request.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function delete(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->delete(...func_get_args());
}

/**
 * Visit the given URI with a DELETE request, expecting a JSON response.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function deleteJson(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->deleteJson(...func_get_args());
}

/**
 * Visit the given URI with a OPTIONS request.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function options(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->options(...func_get_args());
}

/**
 * Visit the given URI with an OPTIONS request, expecting a JSON response.
 *
 * @param  array<string, mixed>  $data
 * @param  array<string, string>  $headers
 */
function optionsJson(string $uri, array $data = [], array $headers = []): TestResponse
{
    return test()->optionsJson(...func_get_args());
}
