<?php

declare(strict_types=1);

namespace Bee\Core\Error;

use Throwable;

interface ThrowableHandler
{
    public function register(): void;

    public function handle(Throwable $throwable): void;
}
