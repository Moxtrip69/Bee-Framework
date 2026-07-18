<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use Bee\Updater\Exception\ValidationException;
use ZipArchive;

final class ArchiveInventoryValidator
{
    private readonly PackagePathValidator $pathValidator;

    public function __construct(?PackagePathValidator $pathValidator = null)
    {
        $this->pathValidator = $pathValidator ?? new PackagePathValidator();
    }

    public function inspect(ZipArchive $archive, ArchiveLimits $limits): ArchiveInventory
    {
        if ($archive->numFiles < 1 || $archive->numFiles > $limits->maximumEntries) {
            throw new ValidationException(['ZIP entry count is outside the permitted range.']);
        }

        $files = [];
        $directories = [];
        $normalizedNames = [];
        $totalBytes = 0;
        $errors = [];

        for ($index = 0; $index < $archive->numFiles; $index++) {
            $stat = $archive->statIndex($index, ZipArchive::FL_ENC_RAW);
            if (!is_array($stat) || !isset($stat['name'], $stat['size'], $stat['comp_size'])) {
                $errors[] = sprintf('Cannot inspect ZIP entry at index %d.', $index);
                continue;
            }

            $name = $stat['name'];
            $isDirectory = str_ends_with($name, '/');
            $path = $isDirectory ? substr($name, 0, -1) : $name;

            if (!$this->pathValidator->isValid($path)) {
                $errors[] = sprintf('Unsafe ZIP entry path: %s.', $name);
                continue;
            }

            $normalizedName = mb_strtolower($path, 'UTF-8');
            if (isset($normalizedNames[$normalizedName])) {
                $errors[] = sprintf('Duplicate or case-colliding ZIP entry: %s.', $name);
                continue;
            }
            $normalizedNames[$normalizedName] = true;

            $size = (int) $stat['size'];
            $compressedSize = (int) $stat['comp_size'];
            if ($size < 0 || $compressedSize < 0 || $size > $limits->maximumFileBytes) {
                $errors[] = sprintf('ZIP entry exceeds its size limit: %s.', $name);
            } elseif ($totalBytes > $limits->maximumUncompressedBytes - $size) {
                $errors[] = 'ZIP exceeds the total uncompressed size limit.';
            } else {
                $totalBytes += $size;
            }

            if ($isDirectory && $size !== 0) {
                $errors[] = sprintf('ZIP directory entry contains data: %s.', $name);
            }

            if ($size > 0 && ($compressedSize === 0
                || $size / $compressedSize > $limits->maximumCompressionRatio)) {
                $errors[] = sprintf('ZIP entry exceeds the compression ratio limit: %s.', $name);
            }

            if ($this->isUnsupportedUnixType($archive, $index, $isDirectory)) {
                $errors[] = sprintf('ZIP links and special files are forbidden: %s.', $name);
            }

            if ($isDirectory) {
                $directories[] = $path;
            } else {
                $files[$name] = $index;
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return new ArchiveInventory($files, $directories);
    }

    private function isUnsupportedUnixType(ZipArchive $archive, int $index, bool $isDirectory): bool
    {
        $operatingSystem = 0;
        $attributes = 0;

        if (!$archive->getExternalAttributesIndex($index, $operatingSystem, $attributes)
            || $operatingSystem !== ZipArchive::OPSYS_UNIX) {
            return false;
        }

        $type = ($attributes >> 16) & 0170000;
        $expectedType = $isDirectory ? 0040000 : 0100000;

        return $type !== 0 && $type !== $expectedType;
    }
}
