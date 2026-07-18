<?php

declare(strict_types=1);

use Bee\Core\Routing\Router;

require __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 2);
$sessionDirectory = __DIR__ . '/.session-modern-' . getmypid();
mkdir($sessionDirectory);
session_save_path($sessionDirectory);

try {
    $applicationRoot = $projectRoot;
    $server = [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_HOST' => 'localhost',
        'REQUEST_URI' => '/Bee-Framework/modern/42',
        'REQUEST_METHOD' => 'GET',
    ];
    $query = ['uri' => 'modern/42'];
    $post = [];
    $application = require $projectRoot . '/app/bootstrap/http.php';
    $application->services->get(Router::class)
        ->get('/modern/{id}', static fn (int $id): array => ['modern' => true, 'id' => $id])
        ->where('id', '\d+')
        ->name('modern.show');

    require_once $projectRoot . '/app/classes/Bee.php';
    ob_start();
    Bee::fly();
    $output = (string) ob_get_clean();

    coreAssert(json_decode($output, true, 8, JSON_THROW_ON_ERROR) === ['modern' => true, 'id' => 42], 'Bee must dispatch a registered modern route.');
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    foreach (glob($sessionDirectory . '/*') ?: [] as $sessionFile) {
        unlink($sessionFile);
    }
    rmdir($sessionDirectory);
}

echo "PASS: Bee dispatches modern routes through the new pipeline\n";
