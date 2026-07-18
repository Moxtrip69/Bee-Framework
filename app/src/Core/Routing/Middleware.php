<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\HttpResponse;

interface Middleware
{
    /** @param callable(HttpRequest): HttpResponse $next */
    public function process(HttpRequest $request, RouteMatch $match, callable $next): HttpResponse;
}
