<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

final class RouteDefinition
{
    /** @var array<string, string> */
    private array $constraints = [];

    /** @var list<string> */
    private array $middleware = [];

    private ?string $routeName = null;

    private string $namePrefix = '';

    /**
     * @param list<HttpMethod> $methods
     * @param callable|array{class-string|string, string}|class-string|string $action
     */
    public function __construct(
        private readonly array $methods,
        private readonly string $path,
        private readonly mixed $action
    ) {
        if ($methods === []) {
            throw new InvalidRouteException('A route must accept at least one HTTP method.');
        }

        if (!is_callable($action) && !is_array($action) && !is_string($action)) {
            throw new InvalidRouteException('A route action must be callable, controller array or invokable class.');
        }
    }

    /** @return list<HttpMethod> */
    public function methods(): array
    {
        return $this->methods;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function action(): mixed
    {
        return $this->action;
    }

    public function name(string $name): self
    {
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/D', $name) !== 1) {
            throw new InvalidRouteException(sprintf('Invalid route name: %s.', $name));
        }

        $this->routeName = $this->namePrefix . $name;

        return $this;
    }

    public function prefixName(string $prefix): self
    {
        $this->namePrefix = $prefix;

        return $this;
    }

    public function routeName(): ?string
    {
        return $this->routeName;
    }

    public function where(string $parameter, string $expression): self
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $parameter) !== 1 || $expression === '') {
            throw new InvalidRouteException('Invalid route parameter constraint.');
        }

        if (@preg_match('~^(?:' . $expression . ')$~D', '') === false) {
            throw new InvalidRouteException(sprintf('Invalid regular expression for route parameter: %s.', $parameter));
        }

        $this->constraints[$parameter] = $expression;

        return $this;
    }

    /** @param string|list<string> $middleware */
    public function middleware(string|array $middleware): self
    {
        foreach ((array) $middleware as $name) {
            if ($name === '') {
                throw new InvalidRouteException('Middleware names cannot be empty.');
            }
            if (!in_array($name, $this->middleware, true)) {
                $this->middleware[] = $name;
            }
        }

        return $this;
    }

    /** @return list<string> */
    public function middlewareNames(): array
    {
        return $this->middleware;
    }

    /** @return array<string, string>|null */
    public function matchPath(string $path): ?array
    {
        [$pattern, $parameters] = $this->compiledPattern();
        if (preg_match($pattern, $path, $matches) !== 1) {
            return null;
        }

        $values = [];
        foreach ($parameters as $parameter) {
            if (isset($matches[$parameter]) && $matches[$parameter] !== '') {
                $value = rawurldecode($matches[$parameter]);
                if (str_contains($value, '/') || str_contains($value, '\\') || str_contains($value, "\0")) {
                    return null;
                }
                $values[$parameter] = $value;
            }
        }

        return $values;
    }

    public function specificity(): int
    {
        $score = 0;
        foreach (explode('/', trim($this->path, '/')) as $segment) {
            $score += str_starts_with($segment, '{') ? 1 : 100;
        }

        return $score;
    }

    /** @return array{string, list<string>} */
    private function compiledPattern(): array
    {
        if ($this->path === '/') {
            return ['~^/$~D', []];
        }

        $pattern = '';
        $parameters = [];
        $segments = explode('/', trim($this->path, '/'));

        foreach ($segments as $index => $segment) {
            if (preg_match('/^\{([A-Za-z_][A-Za-z0-9_]*)(\?)?\}$/D', $segment, $match) !== 1) {
                if (str_contains($segment, '{') || str_contains($segment, '}')) {
                    throw new InvalidRouteException(sprintf('Invalid route parameter segment: %s.', $segment));
                }
                $pattern .= '/' . preg_quote($segment, '~');
                continue;
            }

            $name = $match[1];
            $optional = ($match[2] ?? '') === '?';
            if ($optional && $index !== array_key_last($segments)) {
                throw new InvalidRouteException('Optional route parameters are supported only at the end of a path.');
            }
            if (in_array($name, $parameters, true)) {
                throw new InvalidRouteException(sprintf('Duplicate route parameter: %s.', $name));
            }

            $parameters[] = $name;
            $expression = $this->constraints[$name] ?? '[^/]+';
            $capture = sprintf('(?P<%s>%s)', $name, $expression);
            $pattern .= $optional ? '(?:/' . $capture . ')?' : '/' . $capture;
        }

        return ['~^' . $pattern . '$~Du', $parameters];
    }
}
