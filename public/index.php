<?php

declare(strict_types=1);

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

$app = require __DIR__ . '/../bootstrap/app.php';

// send the response to the browser
(new SapiEmitter)->emit($app);