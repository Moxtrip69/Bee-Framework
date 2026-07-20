<?php

declare(strict_types=1);

use Bee\Core\Exception\CliException;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Security\InstanceKeyManager;

require __DIR__ . '/bootstrap.php';

$testRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bee-instance-keys-' . bin2hex(random_bytes(6));
$configDirectory = $testRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';
mkdir($configDirectory, 0777, true);

$template = implode("\n", [
    'APP_NAME=Test',
    'AUTH_SALT=template-auth',
    'NONCE_SALT=template-nonce',
    'API_PUBLIC_KEY=template-public',
    'API_PRIVATE_KEY=template-private',
    '',
]);
file_put_contents($configDirectory . DIRECTORY_SEPARATOR . '.env.example', $template);

try {
    $root = ApplicationRoot::fromPath($testRoot);
    $manager = new InstanceKeyManager();
    $first = $manager->generate($root);
    $environmentPath = $configDirectory . DIRECTORY_SEPARATOR . '.env';
    $firstContents = (string) file_get_contents($environmentPath);

    coreAssert($first->environmentCreated, 'The environment file must be created from the template.');
    coreAssert(count($first->updatedKeys) === 4, 'All four instance secrets must be generated.');
    coreAssert(!str_contains($firstContents, 'template-auth'), 'Template salts must be replaced.');
    coreAssert((bool) preg_match('/^AUTH_SALT=[a-f0-9]{64}$/m', $firstContents), 'AUTH_SALT must be cryptographically random.');
    coreAssert((bool) preg_match('/^API_PRIVATE_KEY=[a-f0-9]{96}$/m', $firstContents), 'The private API key must use 48 random bytes.');

    $refused = false;
    try {
        $manager->generate($root);
    } catch (CliException) {
        $refused = true;
    }
    coreAssert($refused, 'Existing secrets must not be overwritten without --force.');

    $rotated = $manager->generate($root, true);
    $rotatedContents = (string) file_get_contents($environmentPath);
    coreAssert($rotated->backupPath !== null && is_file($rotated->backupPath), 'Forced rotation must create a backup.');
    coreAssert($firstContents !== $rotatedContents, 'Forced rotation must replace existing secrets.');
    coreAssert(file_get_contents($rotated->backupPath) === $firstContents, 'The backup must preserve the previous environment.');
} finally {
    $files = glob($configDirectory . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT) ?: [];
    $hiddenFiles = glob($configDirectory . DIRECTORY_SEPARATOR . '.*', GLOB_NOSORT) ?: [];
    foreach (array_unique([...$files, ...$hiddenFiles]) as $file) {
        if (basename($file) !== '.' && basename($file) !== '..' && is_file($file)) {
            unlink($file);
        }
    }
    rmdir($configDirectory);
    rmdir(dirname($configDirectory));
    rmdir($testRoot);
}

echo "PASS: instance secrets are generated and rotated safely\n";
