<?php

declare(strict_types=1);

/**
 * @deprecated Use app/bootstrap/http.php, cli.php or testing.php.
 *
 * This compatibility shim keeps legacy integrations that require bee_config.php
 * working while all configuration is now produced by the typed Core bootstrap.
 */
$application = $GLOBALS['bee.application'] ?? null;

if (!$application instanceof \Bee\Core\Foundation\ApplicationContext) {
    $applicationRoot = dirname(__DIR__, 2);
    $server = $_SERVER;
    $application = require dirname(__DIR__) . '/bootstrap/http.php';
}

if (isset($this) && property_exists($this, 'settings')) {
    $this->settings = $application->configuration->environment();
}
