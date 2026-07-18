<?php

declare(strict_types=1);

use Bee\Core\Foundation\ExecutionMode;

require __DIR__ . '/bootstrap.php';

$applicationRoot = dirname(__DIR__, 2);
$environmentOverrides = ['APP_NAME' => 'Bee test application', 'APP_TIMEZONE' => 'UTC'];
$application = require $applicationRoot . '/app/bootstrap/testing.php';

coreAssert($application->mode === ExecutionMode::Test, 'Test mode must be retained.');
coreAssert($application->configuration->application->name === 'Bee test application', 'Tests must accept explicit environment overrides.');
coreAssert(session_status() === PHP_SESSION_NONE, 'Test bootstrap must not start a session.');

echo "PASS: test bootstrap supports isolated overrides\n";
