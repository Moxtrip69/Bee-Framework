<?php

declare(strict_types=1);

use Bee\Core\Http\HttpRequest;
use Bee\Core\Routing\HttpMethod;
use Bee\Core\Routing\Route;
use Bee\Core\Routing\RouteMatch;
use Bee\Core\Routing\Router;
use Bee\Core\Routing\UrlGenerator;

require __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/src/Core/Support/sanitizers.php';
require_once dirname(__DIR__, 2) . '/app/classes/BeeModel.php';
require_once dirname(__DIR__, 2) . '/app/models/exampleArticleModel.php';
require_once dirname(__DIR__, 2) . '/app/controllers/exampleArticleController.php';

foreach ([
    '/app/controllers/exampleArticlePageController.php',
    '/templates/views/examples/articles/indexView.php',
    '/templates/views/examples/articles/showView.php',
    '/templates/views/examples/articles/notFoundView.php',
    '/assets/js/examples/articlesCrud.js',
] as $relativePath) {
    $contents = file_get_contents(dirname(__DIR__, 2) . $relativePath);
    coreAssert(is_string($contents) && mb_check_encoding($contents, 'UTF-8'), 'Example UI files must be valid UTF-8.');
    coreAssert(
        !str_contains($contents, "\xC3\x83") && !str_contains($contents, "\xC3\x82"),
        'Example UI files must not contain double-encoded UTF-8 text.'
    );
}

$pageControllerSource = file_get_contents(
    dirname(__DIR__, 2) . '/app/controllers/exampleArticlePageController.php'
);
coreAssert(
    is_string($pageControllerSource)
        && str_contains($pageControllerSource, 'extends Controller')
        && str_contains($pageControllerSource, '$this->renderToString()')
        && !str_contains($pageControllerSource, 'private function render('),
    'The example page controller must demonstrate the standard Controller and View flow.'
);

$applicationRoot = dirname(__DIR__, 2);
$executionMode = 'test';
$environmentOverrides = ['BEE_ENABLE_EXAMPLE_ROUTES' => 'true'];
$application = require $applicationRoot . '/app/bootstrap/common.php';

$model = new exampleArticleModel([
    'title' => 'Example',
    'views' => '12',
    'metadata' => ['source' => 'test'],
    'id' => 999,
]);
coreAssert($model->views === 12, 'Example model must demonstrate integer casting.');
coreAssert(!isset($model->id), 'Example model must guard its primary key during mass assignment.');

$controller = new exampleArticleController($application->configuration);
$invalid = $controller->store(new HttpRequest(HttpMethod::Post, '/api/examples/articles', [], []));
coreAssert($invalid->status === 422, 'Example controller must validate request input before database access.');

$router = new Router();
Route::useRouter($router);
try {
    require $applicationRoot . '/app/routes/api.php';
    require $applicationRoot . '/app/routes/web.php';
    $router->finalize();
} finally {
    Route::clearRouter();
}

$show = $router->resolve('GET', '/api/examples/articles/15')->match;
coreAssert($show instanceof RouteMatch && $show->parameters['id'] === '15', 'Example show route must bind a numeric ID.');
coreAssert($router->resolve('PATCH', '/api/examples/articles/15')->match instanceof RouteMatch, 'Example update route must accept PATCH.');
coreAssert($router->resolve('DELETE', '/api/examples/articles/abc')->match === null, 'Example routes must reject nonnumeric IDs.');
$web = $router->resolve('GET', '/examples/articles')->match;
coreAssert($web instanceof RouteMatch, 'Example web CRUD route must be registered when examples are enabled.');
coreAssert($web->route->routeName() === 'examples.articles.index', 'Example web route must have a stable name.');
$articlePage = $router->resolve('GET', '/examples/article/bee-modern-routing')->match;
coreAssert($articlePage instanceof RouteMatch, 'Published articles must have a web route by slug.');
coreAssert($articlePage->parameters['slug'] === 'bee-modern-routing', 'Article route must bind the slug.');
coreAssert($router->resolve('GET', '/examples/article/Unsafe_Slug')->match === null, 'Article route must reject unsafe slugs.');
$articleUrl = (new UrlGenerator($router->routes()))
    ->route('examples.article.show', ['slug' => 'bee-modern-routing']);
coreAssert($articleUrl === '/examples/article/bee-modern-routing', 'Named article route must generate its slug URL.');

echo "PASS: modern controller, model and route examples work\n";
