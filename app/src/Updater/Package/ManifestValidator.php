<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use Bee\Updater\Contract\Manifest;
use Bee\Updater\Exception\ValidationException;
use DateTimeImmutable;
use stdClass;

final class ManifestValidator
{
    private const REQUIRED_FIELDS = [
        'format_version', 'package_id', 'product_id', 'channel', 'source_version',
        'target_version', 'bee', 'runtime', 'created_at', 'key_id',
        'checksums_sha256', 'preserve', 'delete', 'migrations', 'health_checks',
    ];

    private const ALLOWED_FIELDS = [...self::REQUIRED_FIELDS, 'expires_at'];

    private const SEMVER_PATTERN = '/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9A-Za-z.-]+)?(?:\+[0-9A-Za-z.-]+)?$/D';

    private readonly PackagePathValidator $pathValidator;

    private readonly PersistentPathPolicy $persistentPathPolicy;

    public function __construct(
        ?PackagePathValidator $pathValidator = null,
        ?PersistentPathPolicy $persistentPathPolicy = null
    ) {
        $this->pathValidator = $pathValidator ?? new PackagePathValidator();
        $this->persistentPathPolicy = $persistentPathPolicy ?? new PersistentPathPolicy();
    }

    public function validate(stdClass $data): Manifest
    {
        $errors = [];
        $fields = array_keys(get_object_vars($data));

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!property_exists($data, $field)) {
                $errors[] = sprintf('Missing manifest field: %s.', $field);
            }
        }

        foreach (array_diff($fields, self::ALLOWED_FIELDS) as $field) {
            $errors[] = sprintf('Unknown manifest field: %s.', $field);
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->validateScalarFields($data, $errors);
        $this->validateBee($data->bee, $errors);
        $this->validateRuntime($data->runtime, $errors);
        $preserve = $this->validateStringList($data->preserve, 'preserve', $errors, true);
        $delete = $this->validateStringList($data->delete, 'delete', $errors, false);
        $this->validateMigrations($data->migrations, $errors);
        $this->validateIdentifierList($data->health_checks, 'health_checks', $errors);

        foreach (array_intersect($preserve, $delete) as $path) {
            $errors[] = sprintf('Path cannot be both preserved and deleted: %s.', $path);
        }

        foreach ($delete as $path) {
            if ($this->persistentPathPolicy->isProtected($path)) {
                $errors[] = sprintf('Persistent path is forbidden in delete: %s.', $path);
            }
        }

        if (is_string($data->source_version) && is_string($data->target_version)
            && $data->source_version === $data->target_version) {
            $errors[] = 'source_version and target_version must be different.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return new Manifest($data);
    }

    /** @param list<string> $errors */
    private function validateScalarFields(stdClass $data, array &$errors): void
    {
        if ($data->format_version !== 1) {
            $errors[] = 'format_version must be integer 1.';
        }

        $patterns = [
            'package_id' => '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D',
            'product_id' => '/^[a-z0-9]+(?:[.-][a-z0-9]+)+$/D',
            'source_version' => '/^[0-9A-Za-z][0-9A-Za-z.+_-]{0,63}$/D',
            'target_version' => '/^[0-9A-Za-z][0-9A-Za-z.+_-]{0,63}$/D',
            'key_id' => '/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/D',
            'checksums_sha256' => '/^[0-9a-f]{64}$/D',
        ];

        foreach ($patterns as $field => $pattern) {
            if (!is_string($data->{$field}) || preg_match($pattern, $data->{$field}) !== 1) {
                $errors[] = sprintf('Invalid manifest field: %s.', $field);
            }
        }

        if (!is_string($data->channel) || !in_array($data->channel, ['stable', 'candidate', 'development'], true)) {
            $errors[] = 'Invalid manifest field: channel.';
        }

        $this->validateDateTime($data->created_at, 'created_at', $errors);
        if (property_exists($data, 'expires_at') && $data->expires_at !== null) {
            $this->validateDateTime($data->expires_at, 'expires_at', $errors);
        }
    }

    /** @param list<string> $errors */
    private function validateBee(mixed $bee, array &$errors): void
    {
        if (!$bee instanceof stdClass || !$this->hasExactFields($bee, ['min', 'max_exclusive'])) {
            $errors[] = 'bee must contain only min and max_exclusive.';
            return;
        }

        foreach (['min', 'max_exclusive'] as $field) {
            if (!is_string($bee->{$field}) || preg_match(self::SEMVER_PATTERN, $bee->{$field}) !== 1) {
                $errors[] = sprintf('bee.%s must be SemVer.', $field);
            }
        }
    }

    /** @param list<string> $errors */
    private function validateRuntime(mixed $runtime, array &$errors): void
    {
        if (!$runtime instanceof stdClass || !$this->hasExactFields($runtime, ['php_min', 'extensions'])) {
            $errors[] = 'runtime must contain only php_min and extensions.';
            return;
        }

        if (!is_string($runtime->php_min) || preg_match(self::SEMVER_PATTERN, $runtime->php_min) !== 1) {
            $errors[] = 'runtime.php_min must be SemVer.';
        }

        $this->validateIdentifierList($runtime->extensions, 'runtime.extensions', $errors, '/^[a-z][a-z0-9_]*$/D');
    }

    /**
     * @param list<string> $errors
     * @return list<string>
     */
    private function validateStringList(mixed $values, string $field, array &$errors, bool $allowGlob): array
    {
        if (!is_array($values)) {
            $errors[] = sprintf('%s must be an array.', $field);
            return [];
        }

        $valid = [];
        foreach ($values as $index => $value) {
            if (!is_string($value) || !$this->pathValidator->isValid($value, $allowGlob)) {
                $errors[] = sprintf('Invalid path at %s[%d].', $field, $index);
                continue;
            }
            $valid[] = $value;
        }

        if (count(array_unique($valid, SORT_STRING)) !== count($valid)) {
            $errors[] = sprintf('%s must not contain duplicates.', $field);
        }

        return $valid;
    }

    /** @param list<string> $errors */
    private function validateMigrations(mixed $migrations, array &$errors): void
    {
        if (!$migrations instanceof stdClass || !$this->hasExactFields($migrations, ['pre', 'post'])) {
            $errors[] = 'migrations must contain only pre and post.';
            return;
        }

        $ids = [];
        foreach (['pre', 'post'] as $phase) {
            if (!is_array($migrations->{$phase})) {
                $errors[] = sprintf('migrations.%s must be an array.', $phase);
                continue;
            }

            foreach ($migrations->{$phase} as $index => $migration) {
                if (!$migration instanceof stdClass || !$this->hasExactFields($migration, ['id', 'engine', 'path', 'rollback_path'])) {
                    $errors[] = sprintf('Invalid migration object at migrations.%s[%d].', $phase, $index);
                    continue;
                }

                if (!is_string($migration->id) || preg_match('/^[a-z0-9][a-z0-9._-]{0,127}$/D', $migration->id) !== 1) {
                    $errors[] = sprintf('Invalid migration id at migrations.%s[%d].', $phase, $index);
                } elseif (isset($ids[$migration->id])) {
                    $errors[] = sprintf('Duplicate migration id: %s.', $migration->id);
                } else {
                    $ids[$migration->id] = true;
                }

                if (!is_string($migration->engine) || !in_array($migration->engine, ['mysql', 'mariadb'], true)) {
                    $errors[] = sprintf('Unsupported migration engine at migrations.%s[%d].', $phase, $index);
                }

                $expectedPrefix = sprintf('migrations/%s/', $phase);
                if (!is_string($migration->path) || !str_starts_with($migration->path, $expectedPrefix)
                    || !str_ends_with($migration->path, '.sql') || !$this->pathValidator->isValid($migration->path)) {
                    $errors[] = sprintf('Invalid migration path at migrations.%s[%d].', $phase, $index);
                }

                if ($migration->rollback_path !== null && (!is_string($migration->rollback_path)
                    || !str_starts_with($migration->rollback_path, 'migrations/rollback/')
                    || !str_ends_with($migration->rollback_path, '.sql')
                    || !$this->pathValidator->isValid($migration->rollback_path))) {
                    $errors[] = sprintf('Invalid rollback path at migrations.%s[%d].', $phase, $index);
                }
            }
        }
    }

    /** @param list<string> $errors */
    private function validateIdentifierList(mixed $values, string $field, array &$errors, string $pattern = '/^[a-z][a-z0-9._-]{0,127}$/D'): void
    {
        if (!is_array($values)) {
            $errors[] = sprintf('%s must be an array.', $field);
            return;
        }

        foreach ($values as $index => $value) {
            if (!is_string($value) || preg_match($pattern, $value) !== 1) {
                $errors[] = sprintf('Invalid identifier at %s[%d].', $field, $index);
            }
        }

        if (count(array_unique($values, SORT_REGULAR)) !== count($values)) {
            $errors[] = sprintf('%s must not contain duplicates.', $field);
        }
    }

    /** @param list<string> $errors */
    private function validateDateTime(mixed $value, string $field, array &$errors): void
    {
        if (!is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/D', $value) !== 1) {
            $errors[] = sprintf('%s must be an RFC 3339 UTC timestamp.', $field);
            return;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $value);
        $dateErrors = DateTimeImmutable::getLastErrors();

        if ($date === false || ($dateErrors !== false
            && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
            $errors[] = sprintf('%s must be an RFC 3339 UTC timestamp.', $field);
        }
    }

    /** @param list<string> $expected */
    private function hasExactFields(stdClass $object, array $expected): bool
    {
        $actual = array_keys(get_object_vars($object));
        sort($actual, SORT_STRING);
        sort($expected, SORT_STRING);

        return $actual === $expected;
    }
}
