<?php

declare(strict_types=1);

namespace Bee\Core\Logging;

use Bee\Core\Exception\LoggingException;
use DateTimeImmutable;
use DateTimeZone;

final readonly class FileLogger implements Logger
{
    public function __construct(private string $path)
    {
    }

    public function log(LogLevel $level, string $message, array $context = []): void
    {
        $directory = dirname($this->path);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new LoggingException(sprintf('Cannot create log directory: %s.', $directory));
        }

        $record = json_encode([
            'timestamp' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM),
            'level' => $level->value,
            'message' => $message,
            'context' => $this->redact($context),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        if (file_put_contents($this->path, $record . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            throw new LoggingException(sprintf('Cannot append to log file: %s.', $this->path));
        }
    }

    /** @return array<string, mixed> */
    private function redact(array $context): array
    {
        $redacted = [];
        foreach ($context as $key => $value) {
            if (preg_match('/pass|secret|token|authorization|cookie|credential/i', (string) $key) === 1) {
                $redacted[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $redacted[$key] = $this->redact($value);
            } elseif (is_scalar($value) || $value === null) {
                $redacted[$key] = $value;
            } else {
                $redacted[$key] = get_debug_type($value);
            }
        }

        return $redacted;
    }
}
