<?php

declare(strict_types=1);

namespace Bee\Core\Logging;

final class NullLogger implements Logger
{
    public function log(LogLevel $level, string $message, array $context = []): void
    {
    }
}
