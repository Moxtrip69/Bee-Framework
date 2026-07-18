<?php

declare(strict_types=1);

namespace Bee\Core\Http;

use Bee\Core\Http\Exception\BadRequestHttpException;
use Bee\Core\Routing\HttpMethod;

final readonly class HttpRequest
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function __construct(
        public HttpMethod $method,
        public string $path,
        public array $query = [],
        public array $body = [],
        public array $headers = []
    ) {
    }

    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     */
    public static function fromInput(array $server, array $query, array $body, HttpConfig $http): self
    {
        $method = HttpMethod::parse(is_string($server['REQUEST_METHOD'] ?? null) ? $server['REQUEST_METHOD'] : 'GET');
        if ($method === HttpMethod::Post && isset($body['_method']) && is_string($body['_method'])) {
            $override = HttpMethod::parse($body['_method']);
            if (!in_array($override, [HttpMethod::Put, HttpMethod::Patch, HttpMethod::Delete], true)) {
                throw new BadRequestHttpException('Invalid HTTP method override.');
            }
            $method = $override;
        }

        $requestPath = parse_url($http->request->requestUri, PHP_URL_PATH);
        if (!is_string($requestPath) || preg_match('/%(?![0-9A-Fa-f]{2})/', $requestPath) === 1) {
            throw new BadRequestHttpException('Malformed request path.');
        }

        $basePath = '/' . trim($http->basePath, '/');
        if ($basePath !== '/' && ($requestPath === $basePath || str_starts_with($requestPath, $basePath . '/'))) {
            $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
        }
        $requestPath = '/' . trim($requestPath, '/');
        if ($requestPath === '//') {
            $requestPath = '/';
        }

        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with((string) $key, 'HTTP_') && is_string($value)) {
                $name = str_replace('_', '-', strtolower(substr((string) $key, 5)));
                $headers[$name] = $value;
            }
        }

        return new self($method, $requestPath, $query, $body, $headers);
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
