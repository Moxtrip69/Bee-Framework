<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$root = dirname(__DIR__, 2);
$entrypoint = file_get_contents($root . '/assets/scss/main.scss');
$settings = file_get_contents($root . '/assets/scss/_theme-settings.scss');
$compiled = file_get_contents($root . '/assets/css/main.min.css');
$coreFunctions = file_get_contents($root . '/app/functions/bee_core_functions.php');

coreAssert(is_string($entrypoint), 'The Sass entrypoint must be readable.');
coreAssert(is_string($settings), 'The Bee theme settings must be readable.');
coreAssert(is_string($compiled), 'The compiled Bee theme must be readable.');
coreAssert(is_string($coreFunctions), 'Core functions must be readable.');
coreAssert(
    strpos($entrypoint, "@import 'theme-settings';") < strpos($entrypoint, "@import 'bootstrap/bootstrap';"),
    'Theme settings must load before Bootstrap.'
);
coreAssert(
    str_contains($settings, '$primary: $bee-ink !default;'),
    'The public theme settings must expose Bootstrap semantic colors.'
);
coreAssert(
    str_contains($settings, '--bee-honey: #{$bee-honey}')
        && str_contains($compiled, '--bee-honey: #f6b900')
        && str_contains($compiled, '.container'),
    'The compiled bundle must contain Bee tokens and Bootstrap components.'
);
coreAssert(
    !is_file($root . '/assets/scss/_tokens.scss') && !str_contains($entrypoint, "@import 'tokens';"),
    'Theme settings must be the only source of Bee design tokens.'
);
coreAssert(
    str_contains($coreFunctions, '<!-- Bee Bootstrap theme: assets/css/main.min.css -->'),
    'The local Bee theme must replace the Bootstrap CDN for bs/bs5.'
);

echo "PASS: Bee theme compiles Bootstrap locally from public settings\n";
