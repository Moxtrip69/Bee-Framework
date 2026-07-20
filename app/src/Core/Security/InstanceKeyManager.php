<?php

declare(strict_types=1);

namespace Bee\Core\Security;

use Bee\Core\Exception\CliException;
use Bee\Core\Foundation\ApplicationRoot;

final class InstanceKeyManager
{
    /** @var array<string, int> */
    private const KEY_BYTES = [
        'AUTH_SALT' => 32,
        'NONCE_SALT' => 32,
        'API_PUBLIC_KEY' => 32,
        'API_PRIVATE_KEY' => 48,
    ];

    public function generate(ApplicationRoot $root, bool $force = false): InstanceKeyResult
    {
        $environmentPath = $root->join('app', 'config', '.env');
        $examplePath = $root->join('app', 'config', '.env.example');
        $environmentCreated = false;

        if (!is_file($environmentPath)) {
            if (!is_file($examplePath)) {
                throw new CliException('Neither app/config/.env nor app/config/.env.example exists.');
            }

            $example = $this->read($examplePath);
            $this->write($environmentPath, $example);
            $environmentCreated = true;
        }

        $contents = $this->read($environmentPath);
        $templateValues = is_file($examplePath)
            ? $this->values($this->read($examplePath))
            : [];
        $currentValues = $this->values($contents);
        $keysToGenerate = [];

        foreach (self::KEY_BYTES as $key => $bytes) {
            $current = trim($currentValues[$key] ?? '');
            $isTemplateValue = isset($templateValues[$key])
                && hash_equals(trim($templateValues[$key]), $current);

            if ($force || $current === '' || $isTemplateValue) {
                $keysToGenerate[$key] = bin2hex(random_bytes($bytes));
            }
        }

        if ($keysToGenerate === []) {
            throw new CliException(
                'Instance keys already exist. Use --force to rotate them intentionally.'
            );
        }

        $backupPath = null;
        if ($force && !$environmentCreated) {
            $backupPath = $this->backupPath($environmentPath);
            $this->write($backupPath, $contents);
        }

        $lineEnding = str_contains($contents, "\r\n") ? "\r\n" : "\n";
        foreach ($keysToGenerate as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            $replacement = $key . '=' . $value;
            if (preg_match($pattern, $contents) === 1) {
                $contents = (string) preg_replace($pattern, $replacement, $contents, 1);
            } else {
                $contents = rtrim($contents, "\r\n") . $lineEnding . $replacement . $lineEnding;
            }
        }

        $this->write($environmentPath, $contents);

        return new InstanceKeyResult(array_keys($keysToGenerate), $environmentCreated, $backupPath);
    }

    private function read(string $path): string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new CliException(sprintf('Unable to read configuration file: %s.', $path));
        }

        return $contents;
    }

    /** @return array<string, string> */
    private function values(string $contents): array
    {
        $values = [];
        foreach (array_keys(self::KEY_BYTES) as $key) {
            if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $contents, $match) === 1) {
                $values[$key] = rtrim($match[1], "\r");
            }
        }

        return $values;
    }

    private function write(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents, LOCK_EX) === false) {
            throw new CliException(sprintf('Unable to write configuration file: %s.', $path));
        }

        @chmod($path, 0600);
    }

    private function backupPath(string $environmentPath): string
    {
        $timestamp = gmdate('YmdHis');
        $candidate = $environmentPath . '.backup.' . $timestamp;
        $suffix = 1;

        while (file_exists($candidate)) {
            $candidate = $environmentPath . '.backup.' . $timestamp . '.' . $suffix++;
        }

        return $candidate;
    }
}
