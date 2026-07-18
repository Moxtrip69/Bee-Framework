<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 2);
$stubPath = $projectRoot . '/stubs/legacy_constants.php';
$stub = file_get_contents($stubPath);
coreAssert(is_string($stub), 'The IDE constants stub must be readable.');
preg_match_all('/^const\s+([A-Z][A-Z0-9_]*)\s*=/m', $stub, $matches);
$stubConstants = $matches[1];
coreAssert(count($stubConstants) === count(array_unique($stubConstants)), 'IDE constants must not be duplicated.');

$applicationRoot = $projectRoot;
$server = [
    'REMOTE_ADDR' => '127.0.0.1',
    'HTTP_HOST' => 'localhost',
    'REQUEST_URI' => '/',
];
require $projectRoot . '/app/bootstrap/http.php';

$requestScoped = ['CSRF_TOKEN', 'CONTROLLER', 'METHOD', 'DOING_AJAX', 'DOING_API', 'DOING_CRON', 'DOING_XML'];
foreach (array_diff($stubConstants, $requestScoped) as $constant) {
    coreAssert(defined($constant), sprintf('IDE stub constant is missing at runtime: %s.', $constant));
}

echo "PASS: IDE constants stub matches runtime compatibility constants\n";
