<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

final readonly class ChecksumMap
{
    /**
     * @param array<string, string> $checksums
     */
    public function __construct(private array $checksums)
    {
    }

    public function checksumFor(string $path): ?string
    {
        return $this->checksums[$path] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->checksums;
    }
}
