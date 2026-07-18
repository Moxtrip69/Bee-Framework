<?php

declare(strict_types=1);

namespace Bee\Updater\Bootstrap;

final readonly class RuntimeEnvironment
{
    /**
     * @param list<string> $loadedExtensions
     */
    public function __construct(
        private string $phpVersion,
        private array $loadedExtensions
    ) {
    }

    public static function detect(): self
    {
        $extensions = get_loaded_extensions();
        $extensions = array_map('strtolower', $extensions);
        sort($extensions, SORT_STRING);

        return new self(PHP_VERSION, array_values($extensions));
    }

    public function phpVersion(): string
    {
        return $this->phpVersion;
    }

    public function hasExtension(string $extension): bool
    {
        return in_array(strtolower($extension), $this->loadedExtensions, true);
    }

    /**
     * @return list<string>
     */
    public function missingExtensions(array $requiredExtensions): array
    {
        return array_values(array_filter(
            $requiredExtensions,
            fn (string $extension): bool => !$this->hasExtension($extension)
        ));
    }
}
