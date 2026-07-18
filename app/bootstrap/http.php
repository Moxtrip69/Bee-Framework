<?php

declare(strict_types=1);

use Bee\Core\Bootstrap\HttpBootstrap;
$server ??= $_SERVER;
$executionMode = 'http';
$application = require __DIR__ . '/common.php';

return (new HttpBootstrap())->boot($application, $server);
