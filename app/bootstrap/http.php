<?php

declare(strict_types=1);

use Bee\Core\Bootstrap\HttpBootstrap;
$server ??= $_SERVER;
$query ??= $_GET;
$post ??= $_POST;
$executionMode = 'http';
$application = require __DIR__ . '/common.php';

return (new HttpBootstrap())->boot($application, $server, $query, $post);
