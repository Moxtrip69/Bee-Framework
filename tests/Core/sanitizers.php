<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/src/Core/Support/sanitizers.php';

coreAssert(sanitize_string("  Hola\0   mundo <b>seguro</b>  ") === 'Hola mundo seguro', 'String sanitizer must remove tags, controls and duplicate whitespace.');
$sanitizedName = sanitize_name("  Mar\u{00ED}a <script>bad()</script> O'Connor-\u{00C1}vila  ");
coreAssert($sanitizedName === "Mar\u{00ED}a bad O'Connor-\u{00C1}vila", 'Unexpected sanitized name: ' . $sanitizedName);
coreAssert(sanitize_phone('+52 (55) 1234-5678') === '+525512345678', 'Phone sanitizer must return a canonical value.');
coreAssert(sanitize_phone('123') === null, 'Phone sanitizer must reject impossible lengths.');
coreAssert(sanitize_email(' USER@Example.COM ') === 'user@example.com', 'Email sanitizer must normalize casing and whitespace.');
coreAssert(sanitize_email('not-an-email') === null, 'Email sanitizer must reject invalid values.');
coreAssert(sanitize_address(" Av. M\u{00E9}xico #20, Col. Centro <b>!</b> ") === "Av. M\u{00E9}xico #20, Col. Centro", 'Address sanitizer must preserve useful punctuation and remove markup.');
coreAssert(sanitize_integer('42', 1, 100) === 42, 'Integer sanitizer must enforce bounds.');
coreAssert(sanitize_integer('42x') === null, 'Integer sanitizer must reject partial numbers.');
coreAssert(sanitize_number('1.234,56') === 1234.56, 'Number sanitizer must normalize common decimal formats.');
coreAssert(sanitize_money('$ 1,234.50 MXN') === '1234.50', 'Money sanitizer must produce a storage-safe decimal string.');
coreAssert(sanitize_boolean('yes') === true && sanitize_boolean('invalid') === null, 'Boolean sanitizer must be strict.');
coreAssert(sanitize_url('https://example.com/path?q=1') === 'https://example.com/path?q=1', 'URL sanitizer must accept HTTP URLs.');
coreAssert(sanitize_url('javascript:alert(1)') === null, 'URL sanitizer must reject unsafe schemes.');
$slug = sanitize_slug("  Programaci\u{00F3}n en PHP 8.2  ");
coreAssert($slug === 'programacion-en-php-8-2', 'Unexpected sanitized slug: ' . $slug);

echo "PASS: common data sanitizers normalize and reject unsafe input\n";
