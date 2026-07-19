<?php

declare(strict_types=1);

namespace Bee\Core\Creator;

use Bee\Core\Creator\Exception\CreatorException;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Routing\HttpMethod;

final readonly class RouteRepository
{
    private string $path;

    public function __construct(ApplicationRoot $root)
    {
        $this->path = $root->join('app', 'routes', 'creator.json');
    }

    /** @return list<array{id: string, methods: list<string>, path: string, controller: string, action: string, name: string, middleware: list<string>, constraints: array<string, string>}> */
    public function all(): array
    {
        if (!is_file($this->path)) {
            return [];
        }
        $contents = file_get_contents($this->path);
        if (!is_string($contents) || trim($contents) === '') {
            return [];
        }
        $routes = json_decode($contents, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($routes) || !array_is_list($routes)) {
            throw new CreatorException('Creator route manifest must contain a JSON list.');
        }

        return array_map(fn (array $route): array => $this->validate($route), $routes);
    }

    /** @param array<string, mixed> $route */
    public function save(array $route, ?string $id = null): array
    {
        $routes = $this->all();
        $route['id'] = $id ?: bin2hex(random_bytes(8));
        $validated = $this->validate($route);
        $found = false;
        foreach ($routes as $index => $existing) {
            if ($existing['id'] === $validated['id']) {
                $routes[$index] = $validated;
                $found = true;
                continue;
            }
            if ($existing['name'] === $validated['name']) {
                throw new CreatorException(sprintf('Route name already exists: %s.', $validated['name']));
            }
        }
        if (!$found) {
            $routes[] = $validated;
        }
        $this->write($routes);

        return $validated;
    }

    public function delete(string $id): void
    {
        $routes = $this->all();
        $remaining = array_values(array_filter(
            $routes,
            static fn (array $route): bool => $route['id'] !== $id
        ));
        if (count($remaining) === count($routes)) {
            throw new CreatorException('Managed route was not found.');
        }
        $this->write($remaining);
    }

    /** @param array<string, mixed> $route */
    private function validate(array $route): array
    {
        $id = (string) ($route['id'] ?? '');
        $rawPath = trim((string) ($route['path'] ?? ''));
        $path = '/' . trim($rawPath, '/');
        $controller = (string) ($route['controller'] ?? '');
        $action = (string) ($route['action'] ?? '');
        $name = (string) ($route['name'] ?? '');
        $methods = array_values(array_unique(array_map(
            static fn (mixed $method): string => HttpMethod::parse((string) $method)->value,
            (array) ($route['methods'] ?? [])
        )));
        $middleware = array_values(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            (array) ($route['middleware'] ?? [])
        )));
        $constraints = (array) ($route['constraints'] ?? []);

        if (preg_match('/^[a-f0-9]{16}$/D', $id) !== 1) {
            throw new CreatorException('Managed route ID is invalid.');
        }
        if ($rawPath === '' || strlen($path) > 255 || str_contains($path, '..') || str_contains($path, '\\')) {
            throw new CreatorException('Managed route path is invalid.');
        }
        if (preg_match('/^(?:[A-Za-z_][A-Za-z0-9_]*\\\\)*[A-Za-z_][A-Za-z0-9_]*Controller$/D', $controller) !== 1) {
            throw new CreatorException('Route controller class is invalid.');
        }
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $action) !== 1) {
            throw new CreatorException('Route controller method is invalid.');
        }
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/D', $name) !== 1) {
            throw new CreatorException('Route name is invalid.');
        }
        if ($methods === []) {
            throw new CreatorException('At least one HTTP method is required.');
        }
        foreach ($middleware as $value) {
            if (preg_match('/^[A-Za-z0-9._-]+$/D', $value) !== 1) {
                throw new CreatorException('Middleware name is invalid.');
            }
        }
        foreach ($constraints as $parameter => $expression) {
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', (string) $parameter) !== 1
                || !is_string($expression)
                || $expression === ''
                || @preg_match('~^(?:' . $expression . ')$~D', '') === false) {
                throw new CreatorException('Route constraint is invalid.');
            }
        }

        return compact('id', 'methods', 'path', 'controller', 'action', 'name', 'middleware', 'constraints');
    }

    /** @param list<array<string, mixed>> $routes */
    private function write(array $routes): void
    {
        $json = json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
        $handle = fopen($this->path, 'c+b');
        if ($handle === false) {
            throw new CreatorException('Unable to open Creator route manifest.');
        }
        try {
            if (!flock($handle, LOCK_EX) || !ftruncate($handle, 0) || rewind($handle) === false) {
                throw new CreatorException('Unable to lock Creator route manifest.');
            }
            if (fwrite($handle, $json) !== strlen($json)) {
                throw new CreatorException('Unable to write Creator route manifest.');
            }
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
    }
}
