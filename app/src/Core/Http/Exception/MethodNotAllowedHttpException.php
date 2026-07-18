<?php

declare(strict_types=1);

namespace Bee\Core\Http\Exception;

final class MethodNotAllowedHttpException extends HttpException
{
    /** @param list<string> $allowedMethods */
    public function __construct(public readonly array $allowedMethods)
    {
        parent::__construct(405, 'Method Not Allowed', ['Allow' => implode(', ', $allowedMethods)]);
    }
}
