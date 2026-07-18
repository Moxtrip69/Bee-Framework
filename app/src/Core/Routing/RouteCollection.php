<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

final class RouteCollection
{
    /** @var list<RouteDefinition> */
    private array $routes = [];

    public function add(RouteDefinition $route): void
    {
        foreach ($this->routes as $existing) {
            if ($existing->path() !== $route->path()) {
                continue;
            }

            $existingMethods = array_map(static fn (HttpMethod $method): string => $method->value, $existing->methods());
            foreach ($route->methods() as $method) {
                if (in_array($method->value, $existingMethods, true)) {
                    throw new InvalidRouteException(sprintf('Duplicate route: %s %s.', $method->value, $route->path()));
                }
            }
        }

        $this->routes[] = $route;
    }

    /** @return list<RouteDefinition> */
    public function all(): array
    {
        $routes = $this->routes;
        usort($routes, static fn (RouteDefinition $left, RouteDefinition $right): int => $right->specificity() <=> $left->specificity());

        return $routes;
    }

    public function named(string $name): ?RouteDefinition
    {
        $found = null;
        foreach ($this->routes as $route) {
            if ($route->routeName() !== $name) {
                continue;
            }
            if ($found !== null) {
                throw new InvalidRouteException(sprintf('Duplicate route name: %s.', $name));
            }
            $found = $route;
        }

        return $found;
    }

    public function assertValid(): void
    {
        $names = [];
        foreach ($this->routes as $route) {
            $route->matchPath($route->path());
            $name = $route->routeName();
            if ($name !== null && isset($names[$name])) {
                throw new InvalidRouteException(sprintf('Duplicate route name: %s.', $name));
            }
            if ($name !== null) {
                $names[$name] = true;
            }
        }
    }
}
