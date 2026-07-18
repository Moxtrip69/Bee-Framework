<?php

declare(strict_types=1);

namespace Bee\Core\Config;

use Bee\Core\Exception\ConfigurationException;
use Bee\Core\Foundation\ApplicationRoot;
use Dotenv\Dotenv;

final class ConfigurationLoader
{
    /** @param array<string, scalar> $overrides */
    public function load(ApplicationRoot $root, array $overrides = []): Configuration
    {
        $configDirectory = $root->join('app', 'config');
        Dotenv::createImmutable($configDirectory)->safeLoad();

        $environment = [];
        foreach (array_replace($_ENV, $overrides) as $key => $value) {
            if (is_scalar($value)) {
                $environment[(string) $key] = (string) $value;
            }
        }

        $identityData = require $root->join('app', 'config', 'identity.php');
        if (!is_array($identityData)) {
            throw new ConfigurationException('app/config/identity.php must return an array.');
        }

        $identity = new FrameworkIdentity(
            $this->requiredIdentityString($identityData, 'product_id'),
            $this->requiredIdentityString($identityData, 'product_version'),
            $this->requiredIdentityString($identityData, 'bee_version'),
            $this->requiredIdentityInt($identityData, 'package_format_version')
        );

        $application = new ApplicationConfig(
            $this->boolean($environment, 'APP_DEBUG', false),
            $this->string($environment, 'APP_NAME', 'Bee application'),
            $this->string($environment, 'APP_CHARSET', 'UTF-8'),
            $this->string($environment, 'APP_TIMEZONE', 'UTC'),
            $this->string($environment, 'APP_LANG', 'en'),
            $this->string($environment, 'APP_DEV_PATH', '/'),
            $this->string($environment, 'APP_LIVE_PATH', '/'),
            $this->string($environment, 'APP_PORT', '')
        );

        return new Configuration(
            $application,
            $this->database($environment, 'LDB_'),
            $this->database($environment, 'DB_'),
            $identity,
            $environment
        );
    }

    /** @param array<string, string> $environment */
    private function database(array $environment, string $prefix): DatabaseConfig
    {
        return new DatabaseConfig(
            $this->string($environment, $prefix . 'ENGINE', 'mysql'),
            $this->string($environment, $prefix . 'HOST', 'localhost'),
            $this->string($environment, $prefix . 'NAME', ''),
            $this->string($environment, $prefix . 'USER', ''),
            $this->string($environment, $prefix . 'PASS', ''),
            $this->string($environment, $prefix . 'CHARSET', 'utf8mb4')
        );
    }

    /** @param array<string, string> $environment */
    private function string(array $environment, string $key, string $default): string
    {
        return $environment[$key] ?? $default;
    }

    /** @param array<string, string> $environment */
    private function boolean(array $environment, string $key, bool $default): bool
    {
        if (!isset($environment[$key])) {
            return $default;
        }

        $value = filter_var($environment[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $value ?? $default;
    }

    /** @param array<string, mixed> $identity */
    private function requiredIdentityString(array $identity, string $key): string
    {
        if (!isset($identity[$key]) || !is_string($identity[$key]) || $identity[$key] === '') {
            throw new ConfigurationException(sprintf('Invalid identity field: %s.', $key));
        }

        return $identity[$key];
    }

    /** @param array<string, mixed> $identity */
    private function requiredIdentityInt(array $identity, string $key): int
    {
        if (!isset($identity[$key]) || !is_int($identity[$key]) || $identity[$key] < 1) {
            throw new ConfigurationException(sprintf('Invalid identity field: %s.', $key));
        }

        return $identity[$key];
    }
}
