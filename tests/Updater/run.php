<?php

declare(strict_types=1);

use Bee\Updater\Bootstrap\ApplicationRoot;
use Bee\Updater\Bootstrap\RuntimeEnvironment;
use Bee\Updater\Bootstrap\UpdaterContext;
use Bee\Updater\Exception\BootstrapException;
use Bee\Updater\Exception\ValidationException;
use Bee\Updater\Package\ChecksumValidator;
use Bee\Updater\Package\ManifestValidator;
use Bee\Updater\Package\PackageInspector;
use Bee\Updater\Package\StrictJsonDecoder;
use Bee\Updater\Security\ArrayTrustedKeyProvider;
use Bee\Updater\Security\SignatureVerifier;

require __DIR__ . '/bootstrap.php';

$tests = [];
$projectRoot = dirname(__DIR__, 2);

$tests['bootstrap uses an explicit root and is independent of cwd'] = static function () use ($projectRoot): void {
    $originalWorkingDirectory = getcwd();
    chdir(sys_get_temp_dir());

    try {
        $applicationRoot = $projectRoot;
        $context = require $projectRoot . '/app/bootstrap/updater.php';
    } finally {
        chdir($originalWorkingDirectory ?: $projectRoot);
    }

    assertTrue($context instanceof UpdaterContext, 'Bootstrap must return an updater context.');
    assertSame(realpath($projectRoot), $context->applicationRoot()->path(), 'Root must be canonical.');
    assertTrue(!defined('REQUEST_URI'), 'Updater bootstrap must not load Bee HTTP configuration.');
    assertTrue(session_status() === PHP_SESSION_NONE, 'Updater bootstrap must not start a session.');
};

$tests['runtime detects updater extensions'] = static function (): void {
    $runtime = RuntimeEnvironment::detect();
    $requiredExtensions = [
        'json',
        'mbstring',
        'openssl',
        'pdo',
        'pdo_mysql',
        'session',
        'fileinfo',
        'hash',
        'sodium',
        'zip',
    ];

    assertTrue(version_compare($runtime->phpVersion(), '8.2.0', '>='), 'PHP 8.2 or newer is required.');
    assertSame([], $runtime->missingExtensions($requiredExtensions), 'Required extensions must load.');
};

$tests['application root rejects invalid paths'] = static function (): void {
    assertThrows(
        static fn (): ApplicationRoot => ApplicationRoot::fromPath(__DIR__ . '/missing'),
        BootstrapException::class,
        'A missing root must be rejected.'
    );
};

$validManifest = [
    'format_version' => 1,
    'package_id' => '018f1f62-1ef6-7d65-b52f-13cb6e65d37a',
    'product_id' => 'mx.joystick.product',
    'channel' => 'stable',
    'source_version' => '1.6.0',
    'target_version' => '1.7.0',
    'bee' => ['min' => '1.6.0', 'max_exclusive' => '2.0.0'],
    'runtime' => ['php_min' => '8.2.0', 'extensions' => ['json', 'sodium', 'zip']],
    'created_at' => '2026-07-18T18:00:00Z',
    'expires_at' => null,
    'key_id' => 'release-2026-01',
    'checksums_sha256' => str_repeat('a', 64),
    'preserve' => ['app/config/.env', 'assets/uploads/**', 'app/logs/**'],
    'delete' => ['app/classes/Legacy.php'],
    'migrations' => [
        'pre' => [],
        'post' => [[
            'id' => '202607180001_add_column',
            'engine' => 'mysql',
            'path' => 'migrations/post/202607180001_add_column.sql',
            'rollback_path' => 'migrations/rollback/202607180001_add_column.sql',
        ]],
    ],
    'health_checks' => ['database', 'bootstrap'],
];

$keyPair = sodium_crypto_sign_keypair();
$secretKey = sodium_crypto_sign_secretkey($keyPair);
$publicKey = sodium_crypto_sign_publickey($keyPair);
$inspector = new PackageInspector(new SignatureVerifier(new ArrayTrustedKeyProvider([
    'release-2026-01' => $publicKey,
])));
$validPackagePayload = [
    'files/app/composer.json' => '{"require":{}}',
    'files/app/composer.lock' => '{"packages":[]}',
    'files/app/vendor/autoload.php' => "<?php\n",
    'files/app/example.php' => "<?php\n\necho 'verified';\n",
    'migrations/post/202607180001_add_column.sql' => 'ALTER TABLE example ADD COLUMN enabled TINYINT NOT NULL DEFAULT 1;',
    'migrations/rollback/202607180001_add_column.sql' => 'ALTER TABLE example DROP COLUMN enabled;',
];

$buildPackage = static function (
    array $manifest,
    array $payload,
    string $signingKey,
    ?array $checksummedPayload = null,
    array $extraEntries = []
): string {
    $checksums = [];
    foreach ($checksummedPayload ?? $payload as $path => $contents) {
        $checksums[$path] = hash('sha256', $contents);
    }
    ksort($checksums, SORT_STRING);

    $checksumsBytes = json_encode($checksums, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $manifest['checksums_sha256'] = hash('sha256', $checksumsBytes);
    $manifestBytes = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $signature = base64_encode(sodium_crypto_sign_detached($manifestBytes, $signingKey));

    $temporaryPath = tempnam(sys_get_temp_dir(), 'bee-package-');
    assertTrue(is_string($temporaryPath), 'A temporary package path must be available.');

    $archive = new ZipArchive();
    assertSame(true, $archive->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE), 'ZIP must be created.');
    $archive->addFromString('manifest.json', $manifestBytes);
    $archive->addFromString('signature.sig', $signature);
    $archive->addFromString('checksums.json', $checksumsBytes);

    foreach ($payload + $extraEntries as $path => $contents) {
        $archive->addFromString($path, $contents);
    }

    $archive->close();
    register_shutdown_function(static function () use ($temporaryPath): void {
        if (is_file($temporaryPath)) {
            unlink($temporaryPath);
        }
    });

    return $temporaryPath;
};

$tests['strict JSON rejects duplicate object keys'] = static function (): void {
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn (): stdClass => $decoder->decodeObject('{"format_version":1,"format_version":1}'),
        ValidationException::class,
        'Duplicate JSON keys must be rejected.'
    );
};

$tests['manifest v1 accepts the formal contract'] = static function () use ($validManifest): void {
    $decoder = new StrictJsonDecoder();
    $manifest = (new ManifestValidator())->validate(
        $decoder->decodeObject(json_encode($validManifest, JSON_THROW_ON_ERROR))
    );

    assertSame('mx.joystick.product', $manifest->productId(), 'Product identity must be retained.');
    assertSame('1.7.0', $manifest->targetVersion(), 'Target version must be retained.');
};

$tests['manifest v1 rejects unknown fields and traversal'] = static function () use ($validManifest): void {
    $invalidManifest = $validManifest;
    $invalidManifest['unexpected'] = true;
    $invalidManifest['delete'] = ['../app/config/.env'];
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn () => (new ManifestValidator())->validate(
            $decoder->decodeObject(json_encode($invalidManifest, JSON_THROW_ON_ERROR))
        ),
        ValidationException::class,
        'Unknown fields and unsafe paths must be rejected.'
    );
};

$tests['checksums v1 accepts safe package paths'] = static function (): void {
    $decoder = new StrictJsonDecoder();
    $checksums = (new ChecksumValidator())->validate($decoder->decodeObject(json_encode([
        'files/app/composer.json' => str_repeat('0', 64),
        'migrations/post/202607180001_add_column.sql' => str_repeat('f', 64),
    ], JSON_THROW_ON_ERROR)));

    assertSame(str_repeat('0', 64), $checksums->checksumFor('files/app/composer.json'), 'Checksum must be retained.');
};

$tests['checksums v1 rejects traversal and noncanonical hashes'] = static function (): void {
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn () => (new ChecksumValidator())->validate($decoder->decodeObject(json_encode([
            'files/../app/config/.env' => str_repeat('A', 64),
        ], JSON_THROW_ON_ERROR))),
        ValidationException::class,
        'Traversal and uppercase hashes must be rejected.'
    );
};

$tests['checksums v1 rejects Windows aliases and case collisions'] = static function (): void {
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn () => (new ChecksumValidator())->validate($decoder->decodeObject(json_encode([
            'files/app/Example.php' => str_repeat('a', 64),
            'files/app/example.php' => str_repeat('b', 64),
            'files/app/data.txt:payload.php' => str_repeat('c', 64),
        ], JSON_THROW_ON_ERROR))),
        ValidationException::class,
        'Case collisions and NTFS alternate data streams must be rejected.'
    );
};

$tests['manifest v1 rejects impossible calendar dates'] = static function () use ($validManifest): void {
    $invalidManifest = $validManifest;
    $invalidManifest['created_at'] = '2026-02-30T18:00:00Z';
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn () => (new ManifestValidator())->validate(
            $decoder->decodeObject(json_encode($invalidManifest, JSON_THROW_ON_ERROR))
        ),
        ValidationException::class,
        'Impossible calendar dates must be rejected.'
    );
};

$tests['formal JSON schemas are valid JSON objects'] = static function () use ($projectRoot): void {
    $decoder = new StrictJsonDecoder();

    foreach (['manifest-v1.schema.json', 'checksums-v1.schema.json'] as $schema) {
        $contents = file_get_contents($projectRoot . '/app/resources/updater/schema/' . $schema);
        assertTrue(is_string($contents), sprintf('Schema must be readable: %s.', $schema));
        $decoder->decodeObject($contents);
    }
};

$tests['package inspector verifies the complete trust chain'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $packagePath = $buildPackage($validManifest, $validPackagePayload, $secretKey);
    $package = $inspector->inspect($packagePath);

    assertSame('018f1f62-1ef6-7d65-b52f-13cb6e65d37a', $package->manifest()->packageId(), 'Signed manifest must be retained.');
    assertSame(count($validPackagePayload), count($package->payloadFiles()), 'Verified payload inventory must be retained.');
};

$tests['package inspector rejects payload checksum mismatch'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $actualPayload = $validPackagePayload;
    $actualPayload['files/app/example.php'] = 'modified';
    $signedPayload = $validPackagePayload;
    $signedPayload['files/app/example.php'] = 'expected';
    $packagePath = $buildPackage($validManifest, $actualPayload, $secretKey, $signedPayload);

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'Modified payload bytes must be rejected.'
    );
};

$tests['package inspector rejects Zip Slip before signature processing'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $packagePath = $buildPackage($validManifest, $validPackagePayload, $secretKey, null, ['../outside.php' => 'unsafe']);

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'Zip Slip entries must be rejected before content is interpreted.'
    );
};

$tests['package inspector rejects files missing from checksums'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $packagePath = $buildPackage($validManifest, $validPackagePayload, $secretKey, null, ['files/app/undeclared.php' => 'extra']);

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'Undeclared ZIP payload must be rejected.'
    );
};

$tests['package contract rejects persistent paths even when checksummed'] = static function (): void {
    $decoder = new StrictJsonDecoder();

    assertThrows(
        static fn () => (new ChecksumValidator())->validate($decoder->decodeObject(json_encode([
            'files/app/config/.env' => str_repeat('a', 64),
        ], JSON_THROW_ON_ERROR))),
        ValidationException::class,
        'Persistent configuration must never enter a signed payload.'
    );
};

$tests['package inspector rejects signatures from another key'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $inspector,
    $buildPackage
): void {
    $untrustedKeyPair = sodium_crypto_sign_keypair();
    $untrustedSecretKey = sodium_crypto_sign_secretkey($untrustedKeyPair);
    $packagePath = $buildPackage($validManifest, $validPackagePayload, $untrustedSecretKey);

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'A cryptographically valid signature from an untrusted key must be rejected.'
    );
};

$tests['package inspector rejects Unix symbolic links'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $packagePath = $buildPackage(
        $validManifest,
        $validPackagePayload,
        $secretKey,
        null,
        ['files/app/link.php' => 'example.php']
    );
    $archive = new ZipArchive();
    assertSame(true, $archive->open($packagePath), 'Malicious ZIP must reopen for fixture setup.');
    $archive->setExternalAttributesName('files/app/link.php', ZipArchive::OPSYS_UNIX, 0120777 << 16);
    $archive->close();

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'Unix symbolic links must be rejected from ZIP metadata.'
    );
};

$tests['package inspector rejects excessive compression ratio'] = static function () use (
    $validManifest,
    $validPackagePayload,
    $secretKey,
    $inspector,
    $buildPackage
): void {
    $compressedPayload = $validPackagePayload;
    $compressedPayload['files/app/example.php'] = str_repeat('A', 1048576);
    $packagePath = $buildPackage($validManifest, $compressedPayload, $secretKey);

    assertThrows(
        static fn () => $inspector->inspect($packagePath),
        ValidationException::class,
        'ZIP entries with an excessive compression ratio must be rejected.'
    );
};

$failures = 0;

foreach ($tests as $name => $test) {
    try {
        $test();
        echo "PASS: {$name}", PHP_EOL;
    } catch (Throwable $exception) {
        $failures++;
        fwrite(STDERR, "FAIL: {$name}: {$exception->getMessage()}" . PHP_EOL);
    }
}

exit($failures === 0 ? 0 : 1);
