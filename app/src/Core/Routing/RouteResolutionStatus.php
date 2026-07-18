<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

enum RouteResolutionStatus
{
    case Matched;
    case NotFound;
    case MethodNotAllowed;
    case AutomaticOptions;
}
