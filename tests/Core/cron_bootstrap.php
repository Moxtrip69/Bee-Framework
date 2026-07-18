<?php

declare(strict_types=1);

use Bee\Core\Foundation\ExecutionMode;

require __DIR__ . '/bootstrap.php';

$applicationRoot = dirname(__DIR__, 2);
$application = require $applicationRoot . '/app/bootstrap/cron.php';

coreAssert($application->mode === ExecutionMode::Cron, 'Cron mode must be retained.');
coreAssert(session_status() === PHP_SESSION_NONE, 'Cron bootstrap must not start a session.');
coreAssert(!defined('REQUEST_URI'), 'Cron bootstrap must not define HTTP request constants.');

echo "PASS: cron reuses common CLI-safe services\n";
