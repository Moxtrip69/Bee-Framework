<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

final class MiddlewareRegistry
{
    /** @var array<string, Middleware> */
    private array $middleware = [];

    public function register(string $name, Middleware $middleware): void
    {
        if ($name === '') {
            throw new InvalidRouteException('Middleware alias cannot be empty.');
        }
        $this->middleware[$name] = $middleware;
    }

    public function get(string $name): Middleware
    {
        return $this->middleware[$name]
            ?? throw new InvalidRouteException(sprintf('Middleware is not registered: %s.', $name));
    }
}
