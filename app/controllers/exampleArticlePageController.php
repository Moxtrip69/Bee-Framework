<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Http\HttpResponse;

/** Serves the browser UI for the asynchronous article CRUD example. */
final class exampleArticlePageController extends Controller
{
    public function __construct(private readonly Configuration $configuration)
    {
        parent::__construct();
        $this->setViewDirectory('examples/articles');
    }

    public function index(): string
    {
        register_scripts([JS . 'examples/articlesCrud.js'], 'CRUD de artículos de ejemplo');

        $this->setTitle('CRUD moderno de artículos');
        $this->setView('index');
        $this->setData([
            'applicationName' => $this->configuration->application->name,
            'apiUrl' => route('api.examples.articles.index'),
            'articleUrlTemplate' => route(
                'examples.article.show',
                ['slug' => 'article-slug']
            ),
        ]);

        return $this->renderToString();
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

        $this->setTitle((string) $article->title);
        $this->setView('show');
        $this->setData([
            'applicationName' => $this->configuration->application->name,
            'article' => $article->toArray(),
            'indexUrl' => route('examples.articles.index'),
        ]);

        return new HttpResponse(
            $this->renderToString(),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    private function notFoundPage(): string
    {
        $this->setTitle('Artículo no encontrado');
        $this->setView('notFound');
        $this->setData([
            'applicationName' => $this->configuration->application->name,
            'indexUrl' => route('examples.articles.index'),
        ]);

        return $this->renderToString();
    }
}
