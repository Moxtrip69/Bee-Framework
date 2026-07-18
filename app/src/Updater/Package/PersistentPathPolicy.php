<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

final class PersistentPathPolicy
{
    public function isProtected(string $applicationPath): bool
    {
        $path = mb_strtolower(str_replace('\\', '/', $applicationPath), 'UTF-8');
        $path = trim($path, '/');

        if ($path === '.env' || str_starts_with($path, 'app/config/.env')) {
            return true;
        }

        foreach (['assets/uploads', 'app/logs'] as $protectedDirectory) {
            if ($path === $protectedDirectory || str_starts_with($path, $protectedDirectory . '/')) {
                return true;
            }
        }

        return false;
    }
}
