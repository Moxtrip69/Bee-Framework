<?php

declare(strict_types=1);

namespace Bee\Updater\Bootstrap;

final readonly class UpdaterContext
{
    public function __construct(
        private ApplicationRoot $applicationRoot,
        private RuntimeEnvironment $runtime
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
}
