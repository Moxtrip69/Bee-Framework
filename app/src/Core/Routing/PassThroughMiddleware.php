<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\HttpResponse;

final class PassThroughMiddleware implements Middleware
{
    public function process(HttpRequest $request, RouteMatch $match, callable $next): HttpResponse
    {
        return $next($request);
    }
}
