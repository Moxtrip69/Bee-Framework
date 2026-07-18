<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

interface Filesystem
{
    public function fileExists(string $path): bool;

    public function directoryExists(string $path): bool;

    public function read(string $path): string;

    public function write(string $path, string $contents): void;

    public function rename(string $source, string $destination): void;
}
