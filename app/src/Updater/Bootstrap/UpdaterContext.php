<?php

declare(strict_types=1);

namespace Bee\Updater\Bootstrap;

use Bee\Core\Foundation\ApplicationContext;

final readonly class UpdaterContext
{
    public function __construct(
        private ApplicationRoot $applicationRoot,
        private RuntimeEnvironment $runtime,
        private ApplicationContext $application
    ) {
    }

    public function applicationRoot(): ApplicationRoot
    {
        return $this->applicationRoot;
    }

    public function runtime(): RuntimeEnvironment
    {
        return $this->runtime;
    }

    public function application(): ApplicationContext
    {
        return $this->application;
    }
}
