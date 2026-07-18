<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

interface Command
{
    public function name(): string;

    public function description(): string;

    /** @param list<string> $arguments */
    public function execute(array $arguments): int;
}
