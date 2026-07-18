<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

enum HttpMethod: string
{
    case Get = 'GET';
    case Head = 'HEAD';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
    case Options = 'OPTIONS';

    public static function parse(string $method): self
    {
        $parsed = self::tryFrom(strtoupper($method));

        return $parsed ?? throw new InvalidRouteException(sprintf('Unsupported HTTP method: %s.', $method));
    }
}
