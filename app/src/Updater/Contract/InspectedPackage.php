<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

final readonly class InspectedPackage
{
    /** @param list<string> $payloadFiles */
    public function __construct(
        private Manifest $manifest,
        private ChecksumMap $checksums,
        private array $payloadFiles
    ) {
    }

    public function manifest(): Manifest
    {
        return $this->manifest;
    }

    public function checksums(): ChecksumMap
    {
        return $this->checksums;
    }

    /** @return list<string> */
    public function payloadFiles(): array
    {
        return $this->payloadFiles;
    }
}
