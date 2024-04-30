<?php

declare(strict_types=1);

use App\Application;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

// run
$app->run();
