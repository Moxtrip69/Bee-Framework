<?php

declare(strict_types=1);

namespace Bee\Core\Config;

final readonly class Configuration
{
    /** @param array<string, string> $environment */
    public function __construct(
        public ApplicationConfig $application,
        public DatabaseConfig $developmentDatabase,
        public DatabaseConfig $productionDatabase,
        public FrameworkIdentity $identity,
        private array $environment
    ) {
    }

    /** @return array<string, string> */
    public function environment(): array
    {
        return $this->environment;
    }

    public function value(string $key, ?string $default = null): ?string
    {
        return $this->environment[$key] ?? $default;
    }
}
