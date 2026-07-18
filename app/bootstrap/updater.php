<?php

declare(strict_types=1);

use Bee\Updater\Bootstrap\ApplicationRoot;
use Bee\Updater\Bootstrap\RuntimeEnvironment;
use Bee\Updater\Bootstrap\UpdaterContext;

if (!isset($applicationRoot) || !is_string($applicationRoot)) {
    throw new InvalidArgumentException('Define $applicationRoot before loading the updater bootstrap.');
}

$resolvedRoot = realpath($applicationRoot);

if ($resolvedRoot === false || !is_dir($resolvedRoot)) {
    throw new RuntimeException(sprintf('Application root does not exist: %s', $applicationRoot));
}

$autoloadPath = $resolvedRoot . DIRECTORY_SEPARATOR . 'app'
    . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException(sprintf('Composer autoloader was not found: %s', $autoloadPath));
}

require_once $autoloadPath;

$root = ApplicationRoot::fromPath($resolvedRoot);

return new UpdaterContext($root, RuntimeEnvironment::detect());
