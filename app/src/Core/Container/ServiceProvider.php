<?php

declare(strict_types=1);

namespace Bee\Core\Container;

interface ServiceProvider
{
    public function register(ServiceContainer $services): void;
}
