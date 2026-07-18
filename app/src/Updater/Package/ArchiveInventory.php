<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

final readonly class ArchiveInventory
{
    /**
     * @param array<string, int> $files File names mapped to their ZIP indexes.
     * @param list<string> $directories
     */
    public function __construct(private array $files, private array $directories)
    {
    }

    /** @return array<string, int> */
    public function files(): array
    {
        return $this->files;
    }

    /** @return list<string> */
    public function directories(): array
    {
        return $this->directories;
    }

    public function indexOf(string $name): ?int
    {
        return $this->files[$name] ?? null;
    }
}
