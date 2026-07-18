<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use Bee\Updater\Contract\InspectedPackage;
use Bee\Updater\Exception\ValidationException;
use Bee\Updater\Security\SignatureVerifier;
use RuntimeException;
use ZipArchive;

final readonly class PackageInspector
{
    public function __construct(
        private SignatureVerifier $signatureVerifier,
        private ArchiveLimits $limits = new ArchiveLimits(),
        private ArchiveInventoryValidator $inventoryValidator = new ArchiveInventoryValidator(),
        private ManifestValidator $manifestValidator = new ManifestValidator(),
        private ChecksumValidator $checksumValidator = new ChecksumValidator()
    ) {
    }

    public function inspect(string $archivePath): InspectedPackage
    {
        if (!is_file($archivePath)) {
            throw new ValidationException([sprintf('Update package does not exist: %s.', $archivePath)]);
        }

        $archive = new ZipArchive();
        $openResult = $archive->open($archivePath, ZipArchive::RDONLY);
        if ($openResult !== true) {
            throw new ValidationException([sprintf('Cannot open update package ZIP (code %s).', $openResult)]);
        }

        try {
            return $this->inspectOpenArchive($archive);
        } finally {
            $archive->close();
        }
    }

    private function inspectOpenArchive(ZipArchive $archive): InspectedPackage
    {
        $inventory = $this->inventoryValidator->inspect($archive, $this->limits);
        $files = $inventory->files();

        foreach ($inventory->directories() as $directory) {
            if ($directory !== 'files' && !str_starts_with($directory, 'files/')
                && $directory !== 'migrations' && !str_starts_with($directory, 'migrations/')) {
                throw new ValidationException([sprintf('Undeclared ZIP directory: %s.', $directory)]);
            }
        }

        foreach (['manifest.json', 'signature.sig', 'checksums.json'] as $requiredFile) {
            if (!array_key_exists($requiredFile, $files)) {
                throw new ValidationException([sprintf('Required package file is missing: %s.', $requiredFile)]);
            }
        }

        $manifestBytes = $this->readEntry($archive, $files['manifest.json'], $this->limits->maximumManifestBytes);
        $signatureBytes = $this->readEntry($archive, $files['signature.sig'], $this->limits->maximumSignatureBytes);

        // key_id is read from untrusted JSON only to select a local key. Trust is not
        // granted until the signature over the exact manifest bytes succeeds.
        $manifestData = (new StrictJsonDecoder($this->limits->maximumManifestBytes))->decodeObject($manifestBytes);
        if (!property_exists($manifestData, 'key_id') || !is_string($manifestData->key_id)
            || preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/D', $manifestData->key_id) !== 1) {
            throw new ValidationException(['Manifest key_id is missing or invalid.']);
        }

        $this->signatureVerifier->verify($manifestBytes, $signatureBytes, $manifestData->key_id);
        $manifest = $this->manifestValidator->validate($manifestData);

        $checksumsBytes = $this->readEntry($archive, $files['checksums.json'], $this->limits->maximumChecksumsBytes);
        if (!hash_equals($manifest->checksumsSha256(), hash('sha256', $checksumsBytes))) {
            throw new ValidationException(['checksums.json does not match the hash signed by the manifest.']);
        }

        $checksumData = (new StrictJsonDecoder($this->limits->maximumChecksumsBytes))->decodeObject($checksumsBytes);
        $checksums = $this->checksumValidator->validate($checksumData);
        $expectedPayload = array_keys($checksums->all());
        $actualPayload = array_values(array_diff(array_keys($files), ['manifest.json', 'signature.sig', 'checksums.json']));
        sort($expectedPayload, SORT_STRING);
        sort($actualPayload, SORT_STRING);

        if ($expectedPayload !== $actualPayload) {
            throw new ValidationException(['ZIP payload and checksums.json must have one-to-one correspondence.']);
        }

        foreach (['files/app/composer.json', 'files/app/composer.lock', 'files/app/vendor/autoload.php'] as $requiredPayload) {
            if (!array_key_exists($requiredPayload, $files)) {
                throw new ValidationException([sprintf('Complete package payload is missing: %s.', $requiredPayload)]);
            }
        }

        foreach (['pre', 'post'] as $phase) {
            foreach ($manifest->data()->migrations->{$phase} as $migration) {
                foreach ([$migration->path, $migration->rollback_path] as $migrationPath) {
                    if ($migrationPath !== null && $checksums->checksumFor($migrationPath) === null) {
                        throw new ValidationException([sprintf('Declared migration is missing from payload: %s.', $migrationPath)]);
                    }
                }
            }
        }

        foreach ($checksums->all() as $path => $expectedChecksum) {
            $actualChecksum = $this->hashEntry($archive, $files[$path]);
            if (!hash_equals($expectedChecksum, $actualChecksum)) {
                throw new ValidationException([sprintf('Payload checksum mismatch: %s.', $path)]);
            }
        }

        return new InspectedPackage($manifest, $checksums, $expectedPayload);
    }

    private function readEntry(ZipArchive $archive, int $index, int $maximumBytes): string
    {
        $stat = $archive->statIndex($index);
        if (!is_array($stat) || (int) ($stat['size'] ?? -1) > $maximumBytes) {
            throw new ValidationException(['ZIP metadata entry exceeds its size limit.']);
        }

        $contents = $archive->getFromIndex($index, $maximumBytes + 1);
        if (!is_string($contents) || strlen($contents) > $maximumBytes) {
            throw new ValidationException(['Cannot read ZIP metadata entry within its size limit.']);
        }

        return $contents;
    }

    private function hashEntry(ZipArchive $archive, int $index): string
    {
        $stream = $archive->getStreamIndex($index);
        if (!is_resource($stream)) {
            throw new RuntimeException('Cannot open verified ZIP entry stream.');
        }

        $hash = hash_init('sha256');
        try {
            if (hash_update_stream($hash, $stream) === false) {
                throw new RuntimeException('Cannot hash ZIP entry stream.');
            }
        } finally {
            fclose($stream);
        }

        return hash_final($hash);
    }
}
