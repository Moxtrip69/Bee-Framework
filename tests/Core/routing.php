<?php

declare(strict_types=1);

use Bee\Core\Routing\Exception\InvalidRouteException;
use Bee\Core\Routing\HttpMethod;
use Bee\Core\Routing\RouteResolutionStatus;
use Bee\Core\Routing\Router;

require __DIR__ . '/bootstrap.php';

$router = new Router();
$router->get('/users/{id}', static fn (string $id): string => $id)
    ->where('id', '\d+')
    ->name('users.show');
$router->get('/users/create', static fn (): string => 'create')
    ->name('users.create');
$router->post('/users', static fn (): string => 'store')
    ->name('users.store');
$router->get('/archive/{year?}', static fn (?string $year = null): ?string => $year);
$router->group(['prefix' => '/api/v1', 'name' => 'api.', 'middleware' => ['api-auth']], static function (Router $router): void {
    $router->get('/reports/{id}', static fn (string $id): string => $id)
        ->where('id', '[a-z0-9-]+')
        ->name('reports.show');
});
$router->finalize();

$static = $router->resolve('GET', '/users/create');
coreAssert($static->status === RouteResolutionStatus::Matched, 'Static route must match.');
coreAssert($static->match?->route->routeName() === 'users.create', 'Static routes must take precedence over parameters.');

$dynamic = $router->resolve(HttpMethod::Get, '/users/42');
coreAssert($dynamic->match?->parameters === ['id' => '42'], 'Named parameters must be extracted.');
coreAssert($router->resolve('GET', '/users/not-a-number')->status === RouteResolutionStatus::NotFound, 'Constraints must reject invalid values.');
coreAssert($router->resolve('GET', '/users/%2Fetc')->status === RouteResolutionStatus::NotFound, 'Encoded slash parameters must be rejected.');

$wrongVerb = $router->resolve('DELETE', '/users/42');
coreAssert($wrongVerb->status === RouteResolutionStatus::MethodNotAllowed, 'Known paths with wrong verbs must produce 405 state.');
coreAssert(in_array(HttpMethod::Get, $wrongVerb->allowedMethods, true), '405 state must expose allowed methods.');
coreAssert(in_array(HttpMethod::Head, $wrongVerb->allowedMethods, true), 'GET routes must implicitly allow HEAD.');

$head = $router->resolve('HEAD', '/users/42');
coreAssert($head->match?->headFallback === true, 'HEAD must fall back to GET when not explicitly registered.');
coreAssert($router->resolve('OPTIONS', '/users/42')->status === RouteResolutionStatus::AutomaticOptions, 'OPTIONS must be generated automatically.');
coreAssert($router->resolve('GET', '/legacy/controller/action')->status === RouteResolutionStatus::NotFound, 'Unknown paths must remain available to legacy fallback.');

$optional = $router->resolve('GET', '/archive');
coreAssert($optional->status === RouteResolutionStatus::Matched && $optional->match?->parameters === [], 'Trailing optional parameters must match when absent.');

$grouped = $router->resolve('GET', '/api/v1/reports/monthly-1');
coreAssert($grouped->match?->route->routeName() === 'api.reports.show', 'Group name prefixes must apply.');
coreAssert($grouped->match?->route->middlewareNames() === ['api-auth'], 'Group middleware must apply.');

try {
    $router->get('/users/{id}', static fn (): null => null);
    throw new RuntimeException('Duplicate method and path must fail.');
} catch (InvalidRouteException) {
}

echo "PASS: modern routing engine resolves verbs and parameters safely\n";
