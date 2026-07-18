<?php

declare(strict_types=1);

use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Http\HttpConfig;

require __DIR__ . '/bootstrap.php';

$applicationRoot = dirname(__DIR__, 2);
$server = [
    'REMOTE_ADDR' => '203.0.113.10',
    'HTTP_HOST' => 'example.test',
    'REQUEST_URI' => '/health?full=1',
    'HTTPS' => 'on',
];
$application = require $applicationRoot . '/app/bootstrap/http.php';
$http = $application->services->get(HttpConfig::class);

coreAssert($application->mode === ExecutionMode::Http, 'HTTP mode must be retained.');
coreAssert($http instanceof HttpConfig, 'HTTP configuration must be registered.');
coreAssert($http->currentUrl === 'https://example.test/health?full=1', 'HTTP values must use the supplied server context.');
coreAssert(session_status() === PHP_SESSION_NONE, 'HTTP bootstrap alone must not start a session.');

echo "PASS: HTTP concerns are isolated in HTTP bootstrap\n";
