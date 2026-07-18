<?php

declare(strict_types=1);

namespace Bee\Core\Container;

use Closure;
use Bee\Core\Exception\ServiceNotFoundException;
use ReflectionClass;
use ReflectionNamedType;

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
            throw new ServiceNotFoundException(sprintf('Service is not registered: %s.', $id));
        }

        $entry = $this->entries[$id];
        $service = $entry instanceof Closure ? $entry($this) : $entry;
        $this->resolved[$id] = $service;

        return $service;
    }

    /** @param class-string $class */
    public function make(string $class): object
    {
        if ($this->has($class)) {
            return $this->get($class);
        }

        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && $this->has($type->getName())) {
                $arguments[] = $this->get($type->getName());
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new ServiceNotFoundException(sprintf(
                'Cannot resolve constructor parameter %s::$%s.',
                $class,
                $parameter->getName()
            ));
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
