<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Http\HttpConfig;
use Bee\Core\Logging\Logger;

final readonly class CoreStatusCommand implements Command
{
    public function __construct(private ApplicationContext $application)
    {
    }

    public function name(): string
    {
        return 'core:status';
    }

    public function description(): string
    {
        return 'Verify that Core configuration and services load without the HTTP runtime.';
    }

    public function execute(array $arguments): int
    {
        echo json_encode([
            'mode' => $this->application->mode->value,
            'root' => $this->application->root->path(),
            'product_id' => $this->application->configuration->identity->productId,
            'product_version' => $this->application->configuration->identity->productVersion,
            'bee_version' => $this->application->configuration->identity->beeVersion,
            'configuration_loaded' => true,
            'logger_loaded' => $this->application->services->has(Logger::class),
            'session_started' => session_status() !== PHP_SESSION_NONE,
            'http_context_loaded' => $this->application->services->has(HttpConfig::class),
            'bee_loaded' => class_exists('Bee', false),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;

        return 0;
    }
}
