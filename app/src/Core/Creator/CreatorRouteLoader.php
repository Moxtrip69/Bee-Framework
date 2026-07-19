<?php

declare(strict_types=1);

namespace Bee\Core\Creator;

use Bee\Core\Routing\Router;

final readonly class CreatorRouteLoader
{
    public function __construct(private RouteRepository $routes)
    {
    }

    public function load(Router $router): void
    {
        foreach ($this->routes->all() as $stored) {
            $route = $router->match(
                $stored['methods'],
                $stored['path'],
                [$stored['controller'], $stored['action']]
            )->name($stored['name']);
            if ($stored['middleware'] !== []) {
                $route->middleware($stored['middleware']);
            }
            foreach ($stored['constraints'] as $parameter => $expression) {
                $route->where($parameter, $expression);
            }
        }
    }
}
