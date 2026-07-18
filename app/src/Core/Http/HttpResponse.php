<?php

declare(strict_types=1);

namespace Bee\Core\Http;

final readonly class HttpResponse
{
    /** @param array<string, string> $headers */
    public function __construct(
        public string $body = '',
        public int $status = 200,
        public array $headers = []
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function json(array $data, int $status = 200, array $headers = []): self
    {
        return new self(
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8', ...$headers]
        );
    }

    public static function noContent(array $headers = []): self
    {
        return new self('', 204, $headers);
    }
}
