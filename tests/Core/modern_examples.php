<?php

declare(strict_types=1);

use Bee\Core\Http\HttpRequest;
use Bee\Core\Routing\HttpMethod;
use Bee\Core\Routing\Route;
use Bee\Core\Routing\RouteMatch;
use Bee\Core\Routing\Router;

require __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/src/Core/Support/sanitizers.php';
require_once dirname(__DIR__, 2) . '/app/classes/BeeModel.php';
require_once dirname(__DIR__, 2) . '/app/models/exampleArticleModel.php';
require_once dirname(__DIR__, 2) . '/app/controllers/exampleArticleController.php';

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
    $router->finalize();
} finally {
    Route::clearRouter();
}

$show = $router->resolve('GET', '/api/examples/articles/15')->match;
coreAssert($show instanceof RouteMatch && $show->parameters['id'] === '15', 'Example show route must bind a numeric ID.');
coreAssert($router->resolve('PATCH', '/api/examples/articles/15')->match instanceof RouteMatch, 'Example update route must accept PATCH.');
coreAssert($router->resolve('DELETE', '/api/examples/articles/abc')->match === null, 'Example routes must reject nonnumeric IDs.');

echo "PASS: modern controller, model and route examples work\n";
