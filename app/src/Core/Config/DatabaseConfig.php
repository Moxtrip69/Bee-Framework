<?php

declare(strict_types=1);

namespace Bee\Core\Config;

final readonly class DatabaseConfig
{
    public function __construct(
        public string $engine,
        public string $host,
        public string $name,
        public string $username,
        public string $password,
        public string $charset
    ) {
    }
}
