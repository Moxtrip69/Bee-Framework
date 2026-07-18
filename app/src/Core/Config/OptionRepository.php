<?php

declare(strict_types=1);

namespace Bee\Core\Config;

interface OptionRepository
{
    public function get(string $key, mixed $default = null): mixed;
}
