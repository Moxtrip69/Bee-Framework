<?php

declare(strict_types=1);

namespace Bee\Core\Http\Exception;

final class BadRequestHttpException extends HttpException
{
    public function __construct(string $message = 'Bad Request')
    {
        parent::__construct(400, $message);
    }
}
