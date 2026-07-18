<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

final readonly class RouteMatch
{
    /** @param array<string, string> $parameters */
    public function __construct(
        public RouteDefinition $route,
        public array $parameters,
        public bool $headFallback = false
    ) {
    }
}
