<?php

declare(strict_types=1);

use Bee\Core\View\Exception\InvalidViewException;
use Bee\Core\View\Exception\ViewNotFoundException;

require __DIR__ . '/bootstrap.php';

$root = dirname(__DIR__, 2);
$fixtures = $root . '/tests/Fixtures/';

define('TEMPLATES', $fixtures);
define('VIEWS', $fixtures . 'views/');
define('CONTROLLER', 'viewtest');
define('USE_TWIG', false);

require_once $root . '/app/classes/BeeHookManager.php';
require_once $root . '/app/classes/View.php';

$data = ['title' => 'Bee', 'message' => '<script>alert(1)</script>'];
$native = View::renderToString('native', $data);

coreAssert(str_contains($native, '<h1>Bee</h1>'), 'Native views must receive data through $d.');
coreAssert(!str_contains($native, '<script>'), 'The native fixture must render escaped user data.');

$twig = View::renderToString('escaped', $data, 'twig');
coreAssert(str_contains($twig, '&lt;script&gt;'), 'Twig output must autoescape HTML by default.');

ob_start();
View::render('native', ['title' => 'Compatible', 'message' => 'Legacy']);
$legacyOutput = (string) ob_get_clean();
coreAssert(str_contains($legacyOutput, 'Compatible'), 'View::render() must preserve direct output.');

try {
    View::renderToString('../secrets', [], 'bee');
    throw new RuntimeException('Path traversal must be rejected.');
} catch (InvalidViewException) {
}

try {
    View::renderToString('missing', [], 'bee');
    throw new RuntimeException('Missing views must raise a specific exception.');
} catch (ViewNotFoundException) {
}

echo "PASS: view renderer is safe, reusable and backwards compatible\n";
