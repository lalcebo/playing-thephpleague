<?php

declare(strict_types=1);

use League\Config\Configuration;
use League\Container\Container;
use League\Event\EventDispatcher;
use Monolog\Logger;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @return Container|mixed
     */
    function app(?string $abstract = null): mixed
    {
        /** @var Container $container */
        global $container;

        if (is_null($abstract)) {
            return $container;
        }

        return $container->get($abstract);
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     */
    function env(string $key, mixed $default = null): mixed
    {
        if (! app('env')->has($key)) {
            return $default;
        }

        return app('env')->get($key);
    }
}

if (! function_exists('config')) {
    /**
     * Get the specified configuration value.
     *
     * @return Configuration|mixed
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return app('config');
        }

        if (! app('config')->exists($key)) {
            return $default;
        }

        return app('config')->get($key);
    }
}

if (! function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @return EventDispatcher|void
     */
    function event(?object $event = null)
    {
        if (is_null($event)) {
            return app('event');
        }

        app('event')->dispatch($event);
    }
}

if (! function_exists('info')) {
    /**
     * Write some information to the log.
     */
    function info(string $message, array $context = []): void
    {
        app('logger')->info($message, $context);
    }
}

if (! function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @return Logger|void
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('logger');
        }

        app('logger')->debug($message, $context);
    }
}

if (! function_exists('runningInConsole')) {
    /**
     * Determine if the application is running in the console.
     */
    function runningInConsole(): bool
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }
}
