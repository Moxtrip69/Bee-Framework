<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/vendor/autoload.php';

function coreAssert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
