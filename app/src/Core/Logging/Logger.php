<?php

declare(strict_types=1);

namespace Bee\Core\Logging;

interface Logger
{
    /** @param array<string, mixed> $context */
    public function log(LogLevel $level, string $message, array $context = []): void;
}
