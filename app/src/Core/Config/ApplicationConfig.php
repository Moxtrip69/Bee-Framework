<?php

declare(strict_types=1);

namespace Bee\Core\Config;

final readonly class ApplicationConfig
{
    public function __construct(
        public bool $debug,
        public string $name,
        public string $charset,
        public string $timezone,
        public string $language,
        public string $developmentPath,
        public string $livePath,
        public string $port
    ) {
    }
}
