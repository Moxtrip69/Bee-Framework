<?php

declare(strict_types=1);

namespace Bee\Core\Config;

final readonly class FrameworkIdentity
{
    public function __construct(
        public string $productId,
        public string $productVersion,
        public string $beeVersion,
        public int $packageFormatVersion
    ) {
    }
}
