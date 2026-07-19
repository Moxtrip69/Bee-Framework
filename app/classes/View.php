<?php

use Bee\Core\View\Exception\InvalidViewException;
use Bee\Core\View\Exception\ViewNotFoundException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class View
{
    private const ENGINE_BEE = 'bee';
    private const ENGINE_TWIG = 'twig';

    private string $baseDir;
    private string $viewsDir;
    private string $controller;
    private string $templateEngine;
    private ?Environment $twigInstance = null;
    private bool $twigExtensionsRegistered = false;

    public function __construct(?string $engine = null, ?string $controller = null)
    {
        $this->baseDir = rtrim((string) TEMPLATES, '/\\') . DIRECTORY_SEPARATOR;
        $this->viewsDir = rtrim((string) VIEWS, '/\\') . DIRECTORY_SEPARATOR;
        $this->controller = $this->normalizeIdentifier(
            $controller ?? (defined('CONTROLLER') ? (string) CONTROLLER : ''),
            'controller'
        );
        $configuredEngine = defined('USE_TWIG') && USE_TWIG === true
            ? self::ENGINE_TWIG
            : self::ENGINE_BEE;
        $this->templateEngine = $this->normalizeEngine($engine ?? $configuredEngine);

        if ($this->templateEngine === self::ENGINE_TWIG) {
            $this->setUpTwigTemplateEngine();
        }
    }

    public function setUpTwigTemplateEngine(): void
    {
        if ($this->twigInstance instanceof Environment) {
            return;
        }

        $this->twigInstance = new Environment(
            new FilesystemLoader($this->baseDir),
            [
                'autoescape' => 'html',
                'charset' => 'UTF-8',
                'strict_variables' => false,
            ]
        );
        $this->registerFunctions();
    }

    /** @deprecated Prefer renderToString() when the generated HTML must be returned. */
    public function renderBeeTemplate(string $view, array $data = []): void
    {
        echo $this->renderBeeTemplateToString($view, $data);
    }

    /** @param array<string, mixed> $data */
    public function renderBeeTemplateToString(string $view, array $data = []): string
    {
        $filename = $this->resolveNativeView($view);

        ob_start();
        try {
            $d = function_exists('to_object') ? to_object($data) : (object) $data;
            require $filename;

            return (string) ob_get_clean();
        } catch (Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }
    }

    /** @deprecated Prefer renderToString() when the generated HTML must be returned. */
    public function renderTwigTemplate(string $view, array $data = []): void
    {
        echo $this->renderTwigTemplateToString($view, $data);
    }

    /** @param array<string, mixed> $data */
    public function renderTwigTemplateToString(string $view, array $data = []): string
    {
        $this->setUpTwigTemplateEngine();
        $this->registerTwigExtensions();
        $template = $this->resolveTwigView($view);

        return $this->twigInstance->render($template, $data);
    }

    public function getTwigFilters(): void
    {
        $this->setUpTwigTemplateEngine();
        BeeHookManager::runHook('on_get_twig_filters', $this->twigInstance);
        $this->twigInstance->addFilter(
            new TwigFilter('md5', static fn (mixed $value): string => md5((string) $value))
        );
    }

    public function getTwigFunctions(): void
    {
        $this->setUpTwigTemplateEngine();
        BeeHookManager::runHook('on_get_twig_functions', $this->twigInstance);
    }

    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = [], ?string $templateEngine = null): void
    {
        echo self::renderToString($view, $data, $templateEngine);
    }

    /** @param array<string, mixed> $data */
    public static function renderToString(
        string $view,
        array $data = [],
        ?string $templateEngine = null,
        ?string $controller = null
    ): string {
        $renderer = new self($templateEngine, $controller);

        return $renderer->templateEngine === self::ENGINE_TWIG
            ? $renderer->renderTwigTemplateToString($view, $data)
            : $renderer->renderBeeTemplateToString($view, $data);
    }

    /** @param array<string, mixed> $data */
    public static function render_twig(string $view, array $data = []): void
    {
        self::render($view, $data, self::ENGINE_TWIG);
    }

    private function registerFunctions(): void
    {
        $functions = get_defined_functions()['user'] ?? [];
        foreach ($functions as $function) {
            if (is_callable($function)) {
                $this->twigInstance->addFunction(new TwigFunction($function, $function));
            }
        }
    }

    private function registerTwigExtensions(): void
    {
        if ($this->twigExtensionsRegistered) {
            return;
        }

        $this->getTwigFilters();
        $this->getTwigFunctions();
        $this->twigExtensionsRegistered = true;
    }

    private function resolveNativeView(string $view): string
    {
        $relativeView = $this->normalizeIdentifier($view, 'view') . 'View.php';
        $controllerDirectory = $this->controllerDirectory();
        $filename = $controllerDirectory . str_replace('/', DIRECTORY_SEPARATOR, $relativeView);
        $resolved = realpath($filename);

        if ($resolved === false || !$this->isInsideDirectory($resolved, $controllerDirectory)) {
            throw new ViewNotFoundException(sprintf(
                'View "%s" was not found for controller "%s".',
                $view,
                $this->controller
            ));
        }

        return $resolved;
    }

    private function resolveTwigView(string $view): string
    {
        $relativeView = $this->normalizeIdentifier($view, 'view') . 'View.twig';
        $controllerDirectory = $this->controllerDirectory();
        $filename = $controllerDirectory . str_replace('/', DIRECTORY_SEPARATOR, $relativeView);
        $resolved = realpath($filename);

        if ($resolved === false || !$this->isInsideDirectory($resolved, $controllerDirectory)) {
            throw new ViewNotFoundException(sprintf(
                'Twig view "%s" was not found for controller "%s".',
                $view,
                $this->controller
            ));
        }

        return 'views/' . $this->controller . '/' . $relativeView;
    }

    private function controllerDirectory(): string
    {
        $directory = $this->viewsDir
            . str_replace('/', DIRECTORY_SEPARATOR, $this->controller)
            . DIRECTORY_SEPARATOR;
        $resolved = realpath($directory);

        if ($resolved === false || !is_dir($resolved)) {
            throw new ViewNotFoundException(sprintf(
                'View directory was not found for controller "%s".',
                $this->controller
            ));
        }

        return rtrim($resolved, '/\\') . DIRECTORY_SEPARATOR;
    }

    private function normalizeEngine(string $engine): string
    {
        $engine = strtolower(trim($engine));
        if (!in_array($engine, [self::ENGINE_BEE, self::ENGINE_TWIG], true)) {
            throw new InvalidViewException(sprintf('Unsupported template engine: "%s".', $engine));
        }

        return $engine;
    }

    private function normalizeIdentifier(string $value, string $type): string
    {
        $value = trim(str_replace('\\', '/', $value), '/');
        if ($value === '' || preg_match('#^[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*$#', $value) !== 1) {
            throw new InvalidViewException(sprintf('Invalid %s identifier: "%s".', $type, $value));
        }

        return $value;
    }

    private function isInsideDirectory(string $filename, string $directory): bool
    {
        $directory = rtrim(realpath($directory) ?: $directory, '/\\') . DIRECTORY_SEPARATOR;

        return str_starts_with(strtolower($filename), strtolower($directory));
    }
}
