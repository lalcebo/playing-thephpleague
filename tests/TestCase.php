<?php

declare(strict_types=1);

namespace Tests;

use App\Application;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Tests\Traits\Application as ApplicationTrait;

abstract class TestCase extends BaseTestCase
{
    use ApplicationTrait;

    protected Application $app;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Application $app */
        $this->app = require __DIR__ . '/../bootstrap/app.php';

        $this->setUpContainer($this->app->getContainer());
        $this->setContainerValue(ServerRequestFactoryInterface::class, function (): ServerRequestFactoryInterface {
            return new ServerRequestFactory();
        });
    }
}
