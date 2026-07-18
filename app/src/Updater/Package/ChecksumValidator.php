<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use Bee\Updater\Contract\ChecksumMap;
use Bee\Updater\Exception\ValidationException;
use stdClass;

final class ChecksumValidator
{
    private readonly PackagePathValidator $pathValidator;

    private readonly PersistentPathPolicy $persistentPathPolicy;

    public function __construct(
        ?PackagePathValidator $pathValidator = null,
        ?PersistentPathPolicy $persistentPathPolicy = null
    ) {
        $this->pathValidator = $pathValidator ?? new PackagePathValidator();
        $this->persistentPathPolicy = $persistentPathPolicy ?? new PersistentPathPolicy();
    }

    public function validate(stdClass $data): ChecksumMap
    {
        $checksums = get_object_vars($data);
        $errors = [];
        $normalizedPaths = [];

        if ($checksums === []) {
            $errors[] = 'checksums.json must contain at least one file.';
        }

        foreach ($checksums as $path => $checksum) {
            if ((!str_starts_with($path, 'files/') && !str_starts_with($path, 'migrations/'))
                || !$this->pathValidator->isValid($path)) {
                $errors[] = sprintf('Invalid checksum path: %s.', $path);
            }

            if (str_starts_with($path, 'files/')
                && $this->persistentPathPolicy->isProtected(substr($path, 6))) {
                $errors[] = sprintf('Persistent path is forbidden in payload: %s.', $path);
            }

            $normalizedPath = strtolower($path);
            if (isset($normalizedPaths[$normalizedPath])) {
                $errors[] = sprintf('Case-insensitive checksum path collision: %s.', $path);
            }
            $normalizedPaths[$normalizedPath] = true;

            if (!is_string($checksum) || preg_match('/^[0-9a-f]{64}$/D', $checksum) !== 1) {
                $errors[] = sprintf('Invalid SHA-256 checksum for: %s.', $path);
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        ksort($checksums, SORT_STRING);

        return new ChecksumMap($checksums);
    }
}
