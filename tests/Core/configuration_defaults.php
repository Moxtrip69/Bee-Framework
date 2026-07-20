<?php

declare(strict_types=1);

use Bee\Core\Config\ConfigurationLoader;
use Bee\Core\Foundation\ApplicationRoot;

require __DIR__ . '/bootstrap.php';

$testRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bee-config-defaults-' . bin2hex(random_bytes(6));
$configDirectory = $testRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';
mkdir($configDirectory, 0777, true);

$identity = <<<'PHP'
<?php
return [
    'product_id' => 'org.bee-framework.test',
    'product_version' => '1.0.0',
    'bee_version' => '1.6.0',
    'package_format_version' => 1,
];
PHP;
$defaults = <<<'PHP'
<?php
return [
    'APP_NAME' => 'Default application',
    'APP_DEBUG' => false,
    'LDB_ENGINE' => 'mysql',
    'LDB_CHARSET' => 'utf8mb4',
    'DB_ENGINE' => 'mysql',
    'DB_CHARSET' => 'utf8mb4',
];
PHP;
file_put_contents($configDirectory . DIRECTORY_SEPARATOR . 'identity.php', $identity);
file_put_contents($configDirectory . DIRECTORY_SEPARATOR . 'defaults.php', $defaults);

try {
    $root = ApplicationRoot::fromPath($testRoot);
    $configuration = (new ConfigurationLoader())->load($root, [
        'APP_NAME' => 'Environment override',
        'APP_DEBUG' => true,
    ]);

    coreAssert($configuration->application->name === 'Environment override', 'Environment values must override defaults.');
    coreAssert($configuration->application->debug, 'Environment booleans must override versioned defaults.');
    coreAssert($configuration->value('APP_DEBUG') === 'true', 'Boolean values must be normalized to strings.');
    coreAssert($configuration->value('LDB_CHARSET') === 'utf8mb4', 'Defaults must remain available through config().');
} finally {
    unlink($configDirectory . DIRECTORY_SEPARATOR . 'identity.php');
    unlink($configDirectory . DIRECTORY_SEPARATOR . 'defaults.php');
    rmdir($configDirectory);
    rmdir(dirname($configDirectory));
    rmdir($testRoot);
}

echo "PASS: versioned defaults and environment overrides share one configuration\n";
