<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$root = dirname(__DIR__, 2);
$login = file_get_contents($root . '/templates/views/login/loginView.php');
$adminHeader = file_get_contents($root . '/templates/includes/admin/header.php');
$adminSidebar = file_get_contents($root . '/templates/includes/admin/sidebar.php');
$users = file_get_contents($root . '/templates/views/admin/usersView.php');
$controller = file_get_contents($root . '/app/controllers/adminController.php');

foreach (compact('login', 'adminHeader', 'adminSidebar', 'users', 'controller') as $source) {
    coreAssert(is_string($source), 'Admin UI sources must be readable.');
}
coreAssert(
    !str_contains($login, 'publicTop.php') && !str_contains($adminHeader, 'sb-admin-2'),
    'Login and active admin layouts must not load SB Admin 2.'
);
coreAssert(
    str_contains($adminSidebar, '$adminNavigation') && str_contains($adminSidebar, 'offcanvas-lg'),
    'Admin navigation must be declarative and responsive.'
);
coreAssert(
    str_contains($users, 'method="post" data-confirm-form=')
        && str_contains($controller, "\$_SERVER['REQUEST_METHOD'] !== 'POST'"),
    'Destructive user actions must require confirmed POST forms.'
);
coreAssert(
    !str_contains($controller, 'Contraseña: <b>%s</b>'),
    'User creation must not echo plaintext passwords in flash messages.'
);

echo "PASS: login and admin use the secure reusable Bee interface\n";
