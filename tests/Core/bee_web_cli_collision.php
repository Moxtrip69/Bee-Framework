<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$root = dirname(__DIR__, 2);
$htaccess = file_get_contents($root . '/.htaccess');

coreAssert($htaccess !== false, 'The root .htaccess must be readable');
coreAssert(
    preg_match('/RewriteRule \^\(bee\(\?:\/\.\*\)\?\)\$/', $htaccess) === 1,
    'Web routes beginning with /bee must be explicitly dispatched'
);

$beeRulePosition = strpos($htaccess, 'RewriteRule ^(bee(?:/.*)?)$');
$physicalFileCondition = strpos($htaccess, 'RewriteCond %{REQUEST_FILENAME} !-f');

coreAssert($beeRulePosition !== false, 'The /bee rewrite rule must exist');
coreAssert($physicalFileCondition !== false, 'The physical-file exclusion must exist');
coreAssert(
    $beeRulePosition < $physicalFileCondition,
    'The /bee rewrite must run before Apache excludes the physical CLI file'
);
coreAssert(is_file($root . '/bee'), 'The CLI entrypoint must remain available');

echo "PASS: web /bee routes and the CLI entrypoint can coexist\n";
