<?php

declare(strict_types=1);

namespace Bee\Core\Container;

use Closure;
use InvalidArgumentException;

final class ServiceContainer
{
    /** @var array<string, object|Closure(self): object> */
    private array $entries = [];

    /** @var array<string, object> */
    private array $resolved = [];

    public function set(string $id, object $service): void
    {
        $this->entries[$id] = $service;
        unset($this->resolved[$id]);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]) || isset($this->resolved[$id]);
    }

    public function get(string $id): object
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if (!isset($this->entries[$id])) {
            throw new InvalidArgumentException(sprintf('Service is not registered: %s.', $id));
        }

        $entry = $this->entries[$id];
        $service = $entry instanceof Closure ? $entry($this) : $entry;
        $this->resolved[$id] = $service;

        return $service;
    }
}
