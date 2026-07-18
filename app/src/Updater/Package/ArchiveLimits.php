<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use InvalidArgumentException;

final readonly class ArchiveLimits
{
    public function __construct(
        public int $maximumEntries = 100000,
        public int $maximumFileBytes = 268435456,
        public int $maximumUncompressedBytes = 2147483648,
        public int $maximumCompressionRatio = 200,
        public int $maximumManifestBytes = 1048576,
        public int $maximumChecksumsBytes = 33554432,
        public int $maximumSignatureBytes = 1024
    ) {
        foreach (get_object_vars($this) as $value) {
            if ($value < 1) {
                throw new InvalidArgumentException('Archive limits must be positive integers.');
            }
        }
    }
}
