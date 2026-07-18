<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

final class PackagePathValidator
{
    private const WINDOWS_RESERVED_NAME = '/^(con|prn|aux|nul|com[1-9]|lpt[1-9])(?:\.|$)/i';

    public function isValid(string $path, bool $allowGlob = false): bool
    {
        if ($path === '' || strlen($path) > 4096 || str_contains($path, "\0")) {
            return false;
        }

        if (str_starts_with($path, '/') || str_starts_with($path, '\\') || preg_match('/^[A-Za-z]:/', $path) === 1) {
            return false;
        }

        if (str_contains($path, '\\') || str_contains($path, ':')
            || preg_match('/[\x00-\x1F\x7F]/', $path) === 1
            || (!$allowGlob && strpbrk($path, '*?[]{}') !== false)) {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return false;
            }

            $name = rtrim($segment, '. ');
            if ($name === '' || $name !== $segment || preg_match(self::WINDOWS_RESERVED_NAME, $name) === 1) {
                return false;
            }
        }

        return true;
    }
}
