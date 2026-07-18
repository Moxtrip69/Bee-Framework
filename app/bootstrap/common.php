<?php

declare(strict_types=1);

use Bee\Core\Bootstrap\CommonBootstrap;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Foundation\ExecutionMode;
$applicationRoot ??= dirname(__DIR__, 2);
$executionMode ??= 'cli';
$environmentOverrides ??= [];

$autoloadPath = $applicationRoot . DIRECTORY_SEPARATOR . 'app'
    . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException(sprintf('Composer autoloader was not found: %s.', $autoloadPath));
}

require_once $autoloadPath;
require_once $applicationRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
    . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'helpers.php';
require_once $applicationRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
    . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'sanitizers.php';

if (is_string($executionMode)) {
    $executionMode = ExecutionMode::from($executionMode);
}

if (!$executionMode instanceof ExecutionMode) {
    throw new InvalidArgumentException('$executionMode must be a Bee Core ExecutionMode.');
}

$root = ApplicationRoot::fromPath($applicationRoot);

return (new CommonBootstrap())->boot($root, $executionMode, $environmentOverrides);
