<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use LogicException;

final class Route
{
    private static ?Router $router = null;

    public static function useRouter(Router $router): void
    {
        self::$router = $router;
    }

    public static function clearRouter(): void
    {
        self::$router = null;
    }

    public static function get(string $path, mixed $action): RouteDefinition
    {
        return self::router()->get($path, $action);
    }

    public static function post(string $path, mixed $action): RouteDefinition
    {
        return self::router()->post($path, $action);
    }

    public static function put(string $path, mixed $action): RouteDefinition
    {
        return self::router()->put($path, $action);
    }

    public static function patch(string $path, mixed $action): RouteDefinition
    {
        return self::router()->patch($path, $action);
    }

    public static function delete(string $path, mixed $action): RouteDefinition
    {
        return self::router()->delete($path, $action);
    }

    public static function options(string $path, mixed $action): RouteDefinition
    {
        return self::router()->options($path, $action);
    }

    /** @param list<HttpMethod|string> $methods */
    public static function match(array $methods, string $path, mixed $action): RouteDefinition
    {
        return self::router()->match($methods, $path, $action);
    }

    public static function any(string $path, mixed $action): RouteDefinition
    {
        return self::router()->any($path, $action);
    }

    public static function prefix(string $prefix): RouteGroupRegistrar
    {
        return (new RouteGroupRegistrar(self::router()))->prefix($prefix);
    }

    public static function name(string $prefix): RouteGroupRegistrar
    {
        return (new RouteGroupRegistrar(self::router()))->name($prefix);
    }

    /** @param string|list<string> $middleware */
    public static function middleware(string|array $middleware): RouteGroupRegistrar
    {
        return (new RouteGroupRegistrar(self::router()))->middleware($middleware);
    }

    /** @param array{prefix?: string, name?: string, middleware?: string|list<string>} $attributes */
    public static function group(array $attributes, callable $callback): void
    {
        self::router()->group($attributes, static fn (Router $router): mixed => $callback());
    }

    private static function router(): Router
    {
        return self::$router ?? throw new LogicException('Route facade has not been initialized.');
    }
}
