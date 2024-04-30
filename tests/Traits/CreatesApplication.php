<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../../bootstrap/app.php';

        return $app;
    }
}
