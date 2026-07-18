<?php

declare(strict_types=1);

namespace Bee\Core\Http\Exception;

use RuntimeException;

class HttpException extends RuntimeException
{
    /** @param array<string, string> $headers */
    public function __construct(
        public readonly int $status,
        string $message,
        public readonly array $headers = []
    ) {
        parent::__construct($message);
    }
}
