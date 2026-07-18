<?php

declare(strict_types=1);

namespace Bee\Core\Foundation;

use Bee\Core\Config\Configuration;
use Bee\Core\Container\ServiceContainer;

final readonly class ApplicationContext
{
    public function __construct(
        public ApplicationRoot $root,
        public ExecutionMode $mode,
        public Configuration $configuration,
        public ServiceContainer $services
    ) {
    }
}
