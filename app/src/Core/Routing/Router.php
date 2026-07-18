<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

final class Router
{
    /** @var list<array{prefix: string, name: string, middleware: list<string>}> */
    private array $groups = [];

    public function __construct(private readonly RouteCollection $routes = new RouteCollection())
    {
    }

    public function get(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Get], $path, $action);
    }

    public function head(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Head], $path, $action);
    }

    public function post(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Post], $path, $action);
    }

    public function put(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Put], $path, $action);
    }

    public function patch(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Patch], $path, $action);
    }

    public function delete(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Delete], $path, $action);
    }

    public function options(string $path, mixed $action): RouteDefinition
    {
        return $this->add([HttpMethod::Options], $path, $action);
    }

    /** @param list<HttpMethod|string> $methods */
    public function match(array $methods, string $path, mixed $action): RouteDefinition
    {
        $parsed = array_map(
            static fn (HttpMethod|string $method): HttpMethod => $method instanceof HttpMethod ? $method : HttpMethod::parse($method),
            $methods
        );

        return $this->add($parsed, $path, $action);
    }

    public function any(string $path, mixed $action): RouteDefinition
    {
        return $this->add(HttpMethod::cases(), $path, $action);
    }

    /**
     * @param array{prefix?: string, name?: string, middleware?: string|list<string>} $attributes
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groups[] = [
            'prefix' => $this->normalizePrefix((string) ($attributes['prefix'] ?? '')),
            'name' => (string) ($attributes['name'] ?? ''),
            'middleware' => array_values((array) ($attributes['middleware'] ?? [])),
        ];

        try {
            $callback($this);
        } finally {
            array_pop($this->groups);
        }
    }

    public function resolve(HttpMethod|string $method, string $path): RouteResolution
    {
        $requestMethod = $method instanceof HttpMethod ? $method : HttpMethod::parse($method);
        $normalizedPath = $this->normalizePath($path);
        $pathMatches = [];
        $headFallback = null;

        foreach ($this->routes->all() as $route) {
            $parameters = $route->matchPath($normalizedPath);
            if ($parameters === null) {
                continue;
            }

            $pathMatches[] = $route;
            $methods = $route->methods();
            if (in_array($requestMethod, $methods, true)) {
                return RouteResolution::matched(new RouteMatch($route, $parameters));
            }

            if ($requestMethod === HttpMethod::Head && in_array(HttpMethod::Get, $methods, true)) {
                $headFallback ??= new RouteMatch($route, $parameters, true);
            }
        }

        if ($headFallback instanceof RouteMatch) {
            return RouteResolution::matched($headFallback);
        }

        if ($pathMatches === []) {
            return RouteResolution::notFound();
        }

        $allowed = $this->allowedMethods($pathMatches);
        if ($requestMethod === HttpMethod::Options) {
            return RouteResolution::automaticOptions($allowed);
        }

        return RouteResolution::methodNotAllowed($allowed);
    }

    public function routes(): RouteCollection
    {
        return $this->routes;
    }

    public function finalize(): void
    {
        $this->routes->assertValid();
    }

    /** @param list<HttpMethod> $methods */
    private function add(array $methods, string $path, mixed $action): RouteDefinition
    {
        $prefix = '';
        $namePrefix = '';
        $middleware = [];
        foreach ($this->groups as $group) {
            $prefix .= $group['prefix'];
            $namePrefix .= $group['name'];
            $middleware = [...$middleware, ...$group['middleware']];
        }

        $route = new RouteDefinition($methods, $this->normalizePath($prefix . '/' . ltrim($path, '/')), $action);
        if ($namePrefix !== '') {
            $route->prefixName($namePrefix);
        }
        if ($middleware !== []) {
            $route->middleware($middleware);
        }

        $this->routes->add($route);

        return $route;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            return '/';
        }
        if (strlen($path) > 4096 || str_contains($path, "\0") || str_contains($path, '\\') || str_contains($path, '//')) {
            throw new InvalidRouteException('Invalid route path.');
        }

        return $path;
    }

    private function normalizePrefix(string $prefix): string
    {
        return $prefix === '' ? '' : '/' . trim($prefix, '/');
    }

    /** @param list<RouteDefinition> $routes @return list<HttpMethod> */
    private function allowedMethods(array $routes): array
    {
        $allowed = [];
        foreach ($routes as $route) {
            foreach ($route->methods() as $method) {
                $allowed[$method->value] = $method;
                if ($method === HttpMethod::Get) {
                    $allowed[HttpMethod::Head->value] = HttpMethod::Head;
                }
            }
        }
        $allowed[HttpMethod::Options->value] = HttpMethod::Options;
        ksort($allowed, SORT_STRING);

        return array_values($allowed);
    }
}
