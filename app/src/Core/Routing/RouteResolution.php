<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

final readonly class RouteResolution
{
    /** @param list<HttpMethod> $allowedMethods */
    private function __construct(
        public RouteResolutionStatus $status,
        public ?RouteMatch $match = null,
        public array $allowedMethods = []
    ) {
    }

    public static function matched(RouteMatch $match): self
    {
        return new self(RouteResolutionStatus::Matched, $match);
    }

    public static function notFound(): self
    {
        return new self(RouteResolutionStatus::NotFound);
    }

    /** @param list<HttpMethod> $allowed */
    public static function methodNotAllowed(array $allowed): self
    {
        return new self(RouteResolutionStatus::MethodNotAllowed, null, $allowed);
    }

    /** @param list<HttpMethod> $allowed */
    public static function automaticOptions(array $allowed): self
    {
        return new self(RouteResolutionStatus::AutomaticOptions, null, $allowed);
    }
}
