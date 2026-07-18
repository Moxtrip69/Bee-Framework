<?php

declare(strict_types=1);

$executionMode ??= 'cli';

if (!in_array($executionMode, ['cli', 'cron'], true)) {
    throw new InvalidArgumentException('CLI bootstrap supports only CLI and cron modes.');
}

return require __DIR__ . '/common.php';
