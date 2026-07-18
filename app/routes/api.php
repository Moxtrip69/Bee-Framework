<?php

declare(strict_types=1);

use Bee\Core\Routing\Route;

if (filter_var(config('BEE_ENABLE_EXAMPLE_ROUTES', 'false'), FILTER_VALIDATE_BOOLEAN)) {
    Route::prefix('/api/examples')
        ->name('api.examples.')
        ->middleware('api')
        ->group(static function (): void {
            Route::get('/articles', [exampleArticleController::class, 'index'])
                ->name('articles.index');
            Route::get('/articles/{id}', [exampleArticleController::class, 'show'])
                ->whereNumber('id')
                ->name('articles.show');
            Route::post('/articles', [exampleArticleController::class, 'store'])
                ->name('articles.store');
            Route::match(['PUT', 'PATCH'], '/articles/{id}', [exampleArticleController::class, 'update'])
                ->whereNumber('id')
                ->name('articles.update');
            Route::delete('/articles/{id}', [exampleArticleController::class, 'destroy'])
                ->whereNumber('id')
                ->name('articles.destroy');
        });
}

/*
Route::prefix('/api/v1')
    ->name('api.')
    ->middleware(['api'])
    ->group(static function (): void {
        Route::get('/status', [apiController::class, 'status'])
            ->name('status');
    });
*/
