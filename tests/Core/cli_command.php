<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 2);
$command = [PHP_BINARY, $projectRoot . '/bee', 'core:status', '--root=' . $projectRoot];
$pipes = [];
$process = proc_open($command, [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
], $pipes, sys_get_temp_dir());
coreAssert(is_resource($process), 'CLI process must start.');
fclose($pipes[0]);
$output = stream_get_contents($pipes[1]);
$errors = stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);
$exitCode = proc_close($process);

coreAssert($exitCode === 0, sprintf('CLI must exit successfully: %s.', $errors));
$status = json_decode($output, true, 16, JSON_THROW_ON_ERROR);
coreAssert($status['mode'] === 'cli', 'CLI command must use CLI mode.');
coreAssert($status['configuration_loaded'] === true, 'CLI command must load typed configuration.');
coreAssert($status['logger_loaded'] === true, 'CLI command must load shared services.');
coreAssert($status['session_started'] === false, 'CLI command must not start a session.');
coreAssert($status['http_context_loaded'] === false, 'CLI command must not load HTTP context.');
coreAssert($status['bee_loaded'] === false, 'CLI command must not load or execute Bee.');
coreAssert($status['root'] === realpath($projectRoot), 'CLI --root must be explicit and canonical.');

echo "PASS: real CLI command meets the phase exit criterion\n";
