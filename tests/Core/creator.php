<?php

declare(strict_types=1);

use Bee\Core\Creator\ComponentScaffolder;
use Bee\Core\Creator\CreatorRouteLoader;
use Bee\Core\Creator\Exception\CreatorException;
use Bee\Core\Creator\RouteRepository;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Routing\Router;
use Bee\Core\Cli\CreateModelCommand;

require __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 2);
$sandbox = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bee_creator_' . bin2hex(random_bytes(6));
$paths = [
    $sandbox . '/app/controllers',
    $sandbox . '/app/models',
    $sandbox . '/app/routes',
    $sandbox . '/templates/views',
    $sandbox . '/templates/modules/bee',
];
foreach ($paths as $path) {
    coreAssert(mkdir($path, 0775, true) || is_dir($path), 'Creator test sandbox must be created.');
}
foreach (['controllerTemplate.txt', 'modernControllerTemplate.txt', 'modernModelTemplate.txt', 'viewTemplate.txt', 'viewTwigTemplate.txt'] as $template) {
    copy(
        $projectRoot . '/templates/modules/bee/' . $template,
        $sandbox . '/templates/modules/bee/' . $template
    );
}

$removeSandbox = static function (string $directory) use (&$removeSandbox): void {
    foreach (scandir($directory) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $path = $directory . DIRECTORY_SEPARATOR . $entry;
        is_dir($path) ? $removeSandbox($path) : unlink($path);
    }
    rmdir($directory);
};

try {
    $root = ApplicationRoot::fromPath($sandbox);
    $creator = new ComponentScaffolder($root);
    $controller = $creator->createController('Blog Post', 'modern', true);
    coreAssert($controller['class'] === 'blogPostController', 'Creator must normalize modern controller names.');
    coreAssert(is_file($controller['path']) && is_file($controller['view']), 'Controller and initial view must be generated.');

    $model = $creator->createModel(
        'Blog Post',
        'blog_posts',
        $creator->parseFields(['title:string', 'views:int', 'metadata:json'])
    );
    $modelSource = file_get_contents($model['path']);
    coreAssert(str_contains($modelSource, "'views' => 'int'"), 'Generated models must contain configured casts.');
    coreAssert(str_contains($modelSource, "protected array \$guarded = ['id']"), 'Generated models must protect their primary key.');

    ob_start();
    $cliStatus = (new CreateModelCommand($creator))->execute([
        'Comment',
        '--table=comments',
        '--fields=body:string,approved:bool',
        '--no-timestamps',
    ]);
    $cliOutput = (string) ob_get_clean();
    coreAssert($cliStatus === 0 && str_contains($cliOutput, 'commentModel'), 'create:model must reuse the scaffolder from CLI.');
    coreAssert(is_file($sandbox . '/app/models/commentModel.php'), 'create:model must create the requested model file.');

    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($controller['path']), $output, $controllerStatus);
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($model['path']), $modelOutput, $modelStatus);
    coreAssert($controllerStatus === 0 && $modelStatus === 0, 'Generated PHP components must have valid syntax.');

    try {
        $creator->createController('Blog Post');
        throw new RuntimeException('Creator must never overwrite an existing component.');
    } catch (CreatorException) {
    }

    $routes = new RouteRepository($root);
    $stored = $routes->save([
        'methods' => ['GET'],
        'path' => '/posts/{id}',
        'controller' => 'blogPostController',
        'action' => 'show',
        'name' => 'posts.show',
        'middleware' => ['auth'],
        'constraints' => ['id' => '\\d+'],
    ]);
    coreAssert(count($routes->all()) === 1, 'Managed routes must be persisted.');
    $router = new Router();
    (new CreatorRouteLoader($routes))->load($router);
    $router->finalize();
    coreAssert($router->resolve('GET', '/posts/42')->match !== null, 'Managed routes must load into the modern router.');
    coreAssert($router->resolve('GET', '/posts/nope')->match === null, 'Managed route constraints must be applied.');
    $routes->delete($stored['id']);
    coreAssert($routes->all() === [], 'Managed routes must be removable.');
} finally {
    $removeSandbox($sandbox);
}

echo "PASS: Creator services generate components and manage routes safely\n";
