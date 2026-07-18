<?php

declare(strict_types=1);

namespace Bee\Core\Foundation;

use Bee\Core\Exception\ConfigurationException;

final readonly class ApplicationRoot
{
    private function __construct(private string $path)
    {
    }

    public static function fromPath(string $path): self
    {
        $resolved = realpath($path);
        if ($resolved === false || !is_dir($resolved . DIRECTORY_SEPARATOR . 'app')) {
            throw new ConfigurationException(sprintf('Invalid Bee application root: %s.', $path));
        }

        return new self(rtrim($resolved, DIRECTORY_SEPARATOR));
    }

    public static function fromBootstrapDirectory(string $bootstrapDirectory): self
    {
        return self::fromPath(dirname($bootstrapDirectory, 2));
    }

    public function path(): string
    {
        return $this->path;
    }

    public function join(string ...$segments): string
    {
        $cleanSegments = array_map(
            static fn (string $segment): string => trim($segment, '\\/'),
            $segments
        );

        return $this->path . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $cleanSegments);
    }
}
