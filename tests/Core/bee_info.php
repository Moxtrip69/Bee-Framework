<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$root = dirname(__DIR__, 2);
$module = file_get_contents($root . '/templates/modules/bee/infoModule.php');
$coreFunctions = file_get_contents($root . '/app/functions/bee_core_functions.php');

coreAssert(is_string($module), 'The Bee information module must be readable.');
coreAssert(is_string($coreFunctions), 'Core functions must be readable.');
coreAssert(
    str_contains($module, 'bee-info-summary') && str_contains($module, 'bee-info-section'),
    'Bee information must provide runtime summaries and grouped sections.'
);
coreAssert(
    str_contains($coreFunctions, "'Sal de seguridad'     => '••••••••'"),
    'Bee information must never expose the configured security salt.'
);

echo "PASS: Bee information is grouped and keeps secrets hidden\n";
