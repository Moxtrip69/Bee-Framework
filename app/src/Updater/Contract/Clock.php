<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
