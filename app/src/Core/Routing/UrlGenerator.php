<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Routing\Exception\InvalidRouteException;

final readonly class UrlGenerator
{
    public function __construct(
        private RouteCollection $routes,
        private string $baseUrl = ''
    ) {
    }

    /** @param array<string, scalar|null> $parameters */
    public function route(string $name, array $parameters = []): string
    {
        $route = $this->routes->named($name);
        if ($route === null) {
            throw new InvalidRouteException(sprintf('Named route does not exist: %s.', $name));
        }

        $used = [];
        $path = preg_replace_callback(
            '/\{([A-Za-z_][A-Za-z0-9_]*)(\?)?\}/',
            static function (array $match) use ($parameters, &$used): string {
                $name = $match[1];
                $optional = ($match[2] ?? '') === '?';
                if (!array_key_exists($name, $parameters) || $parameters[$name] === null || $parameters[$name] === '') {
                    if ($optional) {
                        return '';
                    }
                    throw new InvalidRouteException(sprintf('Missing route parameter: %s.', $name));
                }

                $used[$name] = true;

                return rawurlencode((string) $parameters[$name]);
            },
            $route->path()
        );

        if (!is_string($path)) {
            throw new InvalidRouteException(sprintf('Cannot generate named route: %s.', $name));
        }

        $path = preg_replace('~/+$~', '', $path) ?: '/';
        if ($route->matchPath($path) === null) {
            throw new InvalidRouteException(sprintf('Parameters do not satisfy named route: %s.', $name));
        }

        $query = array_diff_key($parameters, $used);
        $url = rtrim($this->baseUrl, '/') . $path;

        return $query === [] ? $url : $url . '?' . http_build_query($query);
    }
}
