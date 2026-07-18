<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Foundation\ApplicationRoot;

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
            'title' => 'CRUD moderno de artÃ­culos',
            'applicationName' => $this->configuration->application->name,
            'apiUrl' => route('api.examples.articles.index'),
        ];
        $view = $this->root->join('templates', 'views', 'examples', 'articles', 'indexView.php');
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
}
