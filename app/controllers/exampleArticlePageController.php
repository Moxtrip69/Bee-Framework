<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Http\HttpResponse;

/** Serves the browser UI for the asynchronous article CRUD example. */
final class exampleArticlePageController
{
    public function __construct(
        private readonly ApplicationRoot $root,
        private readonly Configuration $configuration
    ) {
    }

    public function index(): string
    {
        $data = [
            'title' => 'CRUD moderno de artículos',
            'applicationName' => $this->configuration->application->name,
            'apiUrl' => route('api.examples.articles.index'),
            'articleUrlTemplate' => route('examples.article.show', ['slug' => 'article-slug']),
        ];
        return $this->render('indexView.php', $data);
    }

    public function show(string $slug): HttpResponse
    {
        $article = exampleArticleModel::where('slug', $slug)
            ->where('status', 'published')
            ->first();
        if (!$article instanceof exampleArticleModel) {
            return new HttpResponse(
                $this->notFoundPage(),
                404,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return new HttpResponse($this->render('showView.php', [
            'title' => (string) $article->title,
            'applicationName' => $this->configuration->application->name,
            'article' => $article->toArray(),
            'indexUrl' => route('examples.articles.index'),
        ]), 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** @param array<string, mixed> $data */
    private function render(string $filename, array $data): string
    {
        $view = $this->root->join('templates', 'views', 'examples', 'articles', $filename);
        if (!is_file($view)) {
            throw new RuntimeException('The example articles view was not found.');
        }

        ob_start();
        try {
            require $view;
            return (string) ob_get_clean();
        } catch (Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }
    }

    private function notFoundPage(): string
    {
        return $this->render('notFoundView.php', [
            'title' => 'Artículo no encontrado',
            'applicationName' => $this->configuration->application->name,
            'indexUrl' => route('examples.articles.index'),
        ]);
    }
}
