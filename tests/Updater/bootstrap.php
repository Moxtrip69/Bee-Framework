<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/vendor/autoload.php';

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf(
            '%s Expected %s, got %s.',
            $message,
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
}

function assertThrows(callable $callback, string $exceptionClass, string $message): void
{
    try {
        $callback();
    } catch (Throwable $exception) {
        assertTrue($exception instanceof $exceptionClass, $message);

        return;
    }

    throw new RuntimeException($message);
}
