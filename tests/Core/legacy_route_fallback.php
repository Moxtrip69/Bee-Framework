<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

final class legacyprobeController
{
    public function index(string $value): void
    {
        echo 'legacy:' . $value;
    }
}

$projectRoot = dirname(__DIR__, 2);
$sessionDirectory = __DIR__ . '/.session-legacy-' . getmypid();
mkdir($sessionDirectory);
session_save_path($sessionDirectory);

try {
    $applicationRoot = $projectRoot;
    $server = [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_HOST' => 'localhost',
        'REQUEST_URI' => '/Bee-Framework/legacyprobe/index/ok',
        'REQUEST_METHOD' => 'GET',
    ];
    $query = ['uri' => 'legacyprobe/index/ok'];
    $post = [];
    $_SERVER = $server;
    $_GET = $query;
    $_POST = $post;
    require $projectRoot . '/app/bootstrap/http.php';
    require_once $projectRoot . '/app/classes/Bee.php';

    ob_start();
    Bee::fly();
    $output = (string) ob_get_clean();

    coreAssert(
        $output === 'legacy:ok',
        sprintf('Unknown modern paths must use legacy router; received %s.', var_export($output, true))
    );
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    foreach (glob($sessionDirectory . '/*') ?: [] as $sessionFile) {
        unlink($sessionFile);
    }
    rmdir($sessionDirectory);
}

echo "PASS: legacy controller routes remain compatible\n";
