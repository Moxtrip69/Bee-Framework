<?php

declare(strict_types=1);

namespace Bee\Core\Error;

use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Logging\Logger;
use Bee\Core\Logging\LogLevel;
use ErrorException;
use Throwable;

final readonly class CentralErrorHandler implements ThrowableHandler
{
    public function __construct(
        private Logger $logger,
        private ExecutionMode $mode,
        private bool $debug
    ) {
    }

    public function register(): void
    {
        set_error_handler($this->handleError(...));
        set_exception_handler($this->handle(...));
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if ((error_reporting() & $severity) === 0) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public function handle(Throwable $throwable): void
    {
        $context = [
            'exception' => $throwable::class,
            'code' => $throwable->getCode(),
        ];
        if ($this->debug) {
            $context['file'] = $throwable->getFile();
            $context['line'] = $throwable->getLine();
            $context['trace'] = $throwable->getTraceAsString();
        }

        try {
            $this->logger->log(LogLevel::Error, $throwable->getMessage(), $context);
        } catch (Throwable) {
            error_log('Bee error handler could not write to the configured logger.');
        }

        $message = $this->debug ? $throwable->getMessage() : 'An internal application error occurred.';
        if (in_array($this->mode, [ExecutionMode::Cli, ExecutionMode::Cron, ExecutionMode::Updater, ExecutionMode::Test], true)) {
            fwrite(STDERR, $message . PHP_EOL);
            return;
        }

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo $message;
    }
}
