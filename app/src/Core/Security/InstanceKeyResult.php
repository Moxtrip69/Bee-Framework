<?php

declare(strict_types=1);

namespace Bee\Core\Security;

final readonly class InstanceKeyResult
{
    /** @param list<string> $updatedKeys */
    public function __construct(
        public array $updatedKeys,
        public bool $environmentCreated,
        public ?string $backupPath
    ) {
    }
}
