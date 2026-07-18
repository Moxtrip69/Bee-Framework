<?php

declare(strict_types=1);

namespace Bee\Updater\Bootstrap;

use Bee\Updater\Exception\BootstrapException;

final readonly class ApplicationRoot
{
    private function __construct(private string $path)
    {
    }

    public static function fromPath(string $path): self
    {
        if ($path === '') {
            throw new BootstrapException('Application root cannot be empty.');
        }

        $resolvedPath = realpath($path);

        if ($resolvedPath === false || !is_dir($resolvedPath)) {
            throw new BootstrapException(sprintf('Application root does not exist: %s', $path));
        }

        foreach (['app', 'templates', 'assets'] as $requiredDirectory) {
            if (!is_dir($resolvedPath . DIRECTORY_SEPARATOR . $requiredDirectory)) {
                throw new BootstrapException(sprintf(
                    'Invalid Bee application root; missing directory: %s',
                    $requiredDirectory
                ));
            }
        }

        return new self(rtrim($resolvedPath, DIRECTORY_SEPARATOR));
    }

    public function path(): string
    {
        return $this->path;
    }

    public function appPath(string $relativePath = ''): string
    {
        $appPath = $this->path . DIRECTORY_SEPARATOR . 'app';

        return $relativePath === ''
            ? $appPath
            : $appPath . DIRECTORY_SEPARATOR . ltrim($relativePath, '\\/');
    }
}
