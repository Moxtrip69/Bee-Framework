<?php

declare(strict_types=1);

namespace Bee\Updater\Exception;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    /**
     * @param list<string> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }

    /**
     * @return list<string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
