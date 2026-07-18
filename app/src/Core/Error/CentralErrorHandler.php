<?php

declare(strict_types=1);

namespace Bee\Core\Error;

use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Logging\Logger;
use Bee\Core\Logging\LogLevel;
use Bee\Core\Http\Exception\HttpException;
use ErrorException;
use Throwable;

final readonly class CentralErrorHandler implements ThrowableHandler
{
    private ErrorResponseContext $responseContext;

    public function __construct(
        private Logger $logger,
        private ExecutionMode $mode,
        private bool $debug,
        ?ErrorResponseContext $responseContext = null
    ) {
        $this->responseContext = $responseContext ?? new ErrorResponseContext();
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

        $status = $throwable instanceof HttpException ? $throwable->status : 500;
        if (!headers_sent()) {
            http_response_code($status);
            if ($throwable instanceof HttpException) {
                foreach ($throwable->headers as $name => $value) {
                    header($name . ': ' . $value, true);
                }
            }
            header($this->responseContext->expectsJson
                ? 'Content-Type: application/json; charset=UTF-8'
                : 'Content-Type: text/plain; charset=UTF-8');
        }
        if ($this->responseContext->expectsJson) {
            $payload = [
                'status' => $status,
                'error' => true,
                'message' => $message,
            ];
            if ($this->debug) {
                $payload['exception'] = $throwable::class;
            }
            echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            return;
        }
        echo $message;
    }
}
