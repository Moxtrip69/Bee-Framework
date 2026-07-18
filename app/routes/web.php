<?php

declare(strict_types=1);

use Bee\Core\Routing\Route;

if (filter_var(config('BEE_ENABLE_EXAMPLE_ROUTES', 'false'), FILTER_VALIDATE_BOOLEAN)) {
    Route::get('/examples/articles', [exampleArticlePageController::class, 'index'])
        ->name('examples.articles.index');
    Route::get('/examples/article/{slug}', [exampleArticlePageController::class, 'show'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('examples.article.show');
}

/*
Route::get('/welcome', [homeController::class, 'index'])
    ->name('welcome');
*/
