<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Foundation\ExecutionMode;

require __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 2);
$originalDirectory = getcwd();
chdir(sys_get_temp_dir());

try {
    $applicationRoot = $projectRoot;
    $application = require $projectRoot . '/app/bootstrap/cli.php';
} finally {
    chdir($originalDirectory ?: $projectRoot);
}

coreAssert($application instanceof ApplicationContext, 'CLI must return an application context.');
coreAssert($application->mode === ExecutionMode::Cli, 'CLI mode must be retained.');
coreAssert($application->configuration instanceof Configuration, 'CLI must load typed configuration.');
coreAssert($application->root->path() === realpath($projectRoot), 'CLI root must not depend on cwd.');
coreAssert(session_status() === PHP_SESSION_NONE, 'CLI must not start a session.');
coreAssert(!defined('REQUEST_URI'), 'CLI must not define HTTP request constants.');
coreAssert(!isset($GLOBALS['bee']), 'CLI must not instantiate Bee.');

echo "PASS: CLI bootstrap is independent from HTTP\n";
