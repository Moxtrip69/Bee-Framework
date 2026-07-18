<?php

declare(strict_types=1);

use Bee\Core\Routing\Route;
use Bee\Core\Routing\RouteResolutionStatus;
use Bee\Core\Routing\Router;
use Bee\Core\Routing\UrlGenerator;

require __DIR__ . '/bootstrap.php';

$router = new Router();
Route::useRouter($router);

Route::get('/', static fn (): string => 'home')->name('home');
Route::prefix('/api/v1')
    ->name('api.')
    ->middleware(['api-auth'])
    ->group(static function (): void {
        Route::get('/users/{id}', static fn (string $id): string => $id)
            ->where('id', '\d+')
            ->name('users.show');
        Route::match(['PUT', 'PATCH'], '/users/{id}', static fn (string $id): string => $id)
            ->where('id', '\d+')
            ->name('users.update');
    });
Route::get('/archive/{year?}', static fn (?string $year = null): ?string => $year)
    ->where('year', '\d{4}')
    ->name('archive');

$router->finalize();
Route::clearRouter();

$match = $router->resolve('PATCH', '/api/v1/users/7');
coreAssert($match->status === RouteResolutionStatus::Matched, 'Facade must register multi-verb routes.');
coreAssert($match->match?->route->routeName() === 'api.users.update', 'Facade groups must prefix route names.');

$urls = new UrlGenerator($router->routes(), 'https://example.test');
coreAssert($urls->route('api.users.show', ['id' => 42]) === 'https://example.test/api/v1/users/42', 'Named routes must generate absolute URLs.');
coreAssert($urls->route('archive', ['page' => 2]) === 'https://example.test/archive?page=2', 'Unused parameters must become a query string.');

try {
    Route::get('/unavailable', static fn (): null => null);
    throw new RuntimeException('Cleared facade must not retain global router state.');
} catch (LogicException) {
}

echo "PASS: Laravel-style Route facade and URL generation work\n";
