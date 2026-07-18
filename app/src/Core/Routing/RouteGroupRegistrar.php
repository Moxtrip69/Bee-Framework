<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

final class RouteGroupRegistrar
{
    /** @var array{prefix?: string, name?: string, middleware?: list<string>} */
    private array $attributes = [];

    public function __construct(private readonly Router $router)
    {
    }

    public function prefix(string $prefix): self
    {
        $this->attributes['prefix'] = $prefix;

        return $this;
    }

    public function name(string $prefix): self
    {
        $this->attributes['name'] = $prefix;

        return $this;
    }

    /** @param string|list<string> $middleware */
    public function middleware(string|array $middleware): self
    {
        $this->attributes['middleware'] = [
            ...($this->attributes['middleware'] ?? []),
            ...(array) $middleware,
        ];

        return $this;
    }

    public function group(callable $callback): void
    {
        $this->router->group($this->attributes, static fn (Router $router): mixed => $callback());
    }
}
