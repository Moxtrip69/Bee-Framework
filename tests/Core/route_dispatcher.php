<?php

declare(strict_types=1);

use Bee\Core\Config\ApplicationConfig;
use Bee\Core\Container\ServiceContainer;
use Bee\Core\Http\Exception\BadRequestHttpException;
use Bee\Core\Http\HttpConfig;
use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\HttpRequestContext;
use Bee\Core\Http\HttpResponse;
use Bee\Core\Routing\Middleware;
use Bee\Core\Routing\MiddlewareRegistry;
use Bee\Core\Routing\RouteDispatcher;
use Bee\Core\Routing\RouteMatch;
use Bee\Core\Routing\Router;

require __DIR__ . '/bootstrap.php';

$router = new Router();
$route = $router->get('/users/{id}', static function (int $id, HttpRequest $request): array {
    return ['id' => $id, 'method' => $request->method->value];
})->where('id', '\d+')->middleware('audit');
$match = $router->resolve('GET', '/users/42')->match;
coreAssert($match instanceof RouteMatch, 'Test route must match.');

$registry = new MiddlewareRegistry();
$registry->register('audit', new class implements Middleware {
    public function process(HttpRequest $request, RouteMatch $match, callable $next): HttpResponse
    {
        $response = $next($request);

        return new HttpResponse($response->body, $response->status, [...$response->headers, 'X-Audit' => 'passed']);
    }
});

$request = new HttpRequest(\Bee\Core\Routing\HttpMethod::Get, '/users/42');
$response = (new RouteDispatcher(new ServiceContainer(), $registry))->dispatch($match, $request);
coreAssert($response->status === 200, 'Dispatcher must produce a successful response.');
coreAssert($response->headers['Content-Type'] === 'application/json; charset=UTF-8', 'Arrays must become JSON responses.');
coreAssert($response->headers['X-Audit'] === 'passed', 'Route middleware must wrap the action.');
coreAssert(json_decode($response->body, true, 8, JSON_THROW_ON_ERROR) === ['id' => 42, 'method' => 'GET'], 'Typed parameters and request injection must work.');

$applicationConfig = new ApplicationConfig(false, 'Test', 'UTF-8', 'UTC', 'es', '/Bee/', '/', '');
$httpConfig = new HttpConfig(new HttpRequestContext('127.0.0.1', 'localhost', '/Bee/users/42', false), $applicationConfig);
$overridden = HttpRequest::fromInput(
    ['REQUEST_METHOD' => 'POST'],
    [],
    ['_method' => 'PATCH'],
    $httpConfig
);
coreAssert($overridden->method === \Bee\Core\Routing\HttpMethod::Patch, 'POST method override must support PATCH.');
coreAssert($overridden->path === '/users/42', 'HTTP request path must remove the configured base path.');

try {
    HttpRequest::fromInput(['REQUEST_METHOD' => 'POST'], [], ['_method' => 'GET'], $httpConfig);
    throw new RuntimeException('Unsafe method override must fail.');
} catch (BadRequestHttpException) {
}

echo "PASS: route dispatcher, middleware and HTTP request abstraction work\n";
