<?php

declare(strict_types=1);

namespace Bee\Core\Http;

final readonly class HttpRequestContext
{
    public function __construct(
        public string $clientAddress,
        public string $host,
        public string $requestUri,
        public bool $secure
    ) {
    }

    /** @param array<string, mixed> $server */
    public static function fromServer(array $server): self
    {
        return new self(
            is_string($server['REMOTE_ADDR'] ?? null) ? $server['REMOTE_ADDR'] : '',
            is_string($server['HTTP_HOST'] ?? null) ? $server['HTTP_HOST'] : 'localhost',
            is_string($server['REQUEST_URI'] ?? null) ? $server['REQUEST_URI'] : '/',
            ($server['HTTPS'] ?? null) === 'on'
        );
    }

    public function isLocal(): bool
    {
        return in_array($this->clientAddress, ['127.0.0.1', '::1'], true);
    }
}
