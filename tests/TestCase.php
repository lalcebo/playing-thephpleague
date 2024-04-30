<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Traits\CreatesApplication;
use Tests\Traits\RequestsTest;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RequestsTest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createApplication();
    }
}
