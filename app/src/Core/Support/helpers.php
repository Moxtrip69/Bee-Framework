<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Config\OptionRepository;
use Bee\Core\Exception\ConfigurationException;
use Bee\Core\Foundation\ApplicationContext;

if (!function_exists('config')) {
    function config(?string $key = null, ?string $default = null): mixed
    {
        $application = $GLOBALS['bee.application'] ?? null;
        if (!$application instanceof ApplicationContext) {
            throw new ConfigurationException('Bee Core bootstrap has not been initialized.');
        }

        $configuration = $application->services->get(Configuration::class);

        return $key === null ? $configuration : $configuration->value($key, $default);
    }
}

if (!function_exists('option')) {
    function option(string $key, mixed $default = null): mixed
    {
        $application = $GLOBALS['bee.application'] ?? null;
        if (!$application instanceof ApplicationContext) {
            throw new ConfigurationException('Bee Core bootstrap has not been initialized.');
        }

        if (!$application->services->has(OptionRepository::class)) {
            return $default;
        }

        return $application->services->get(OptionRepository::class)->get($key, $default);
    }
}
