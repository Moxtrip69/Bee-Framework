<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Exception\CliException;

final class CliOptions
{
    /** @param list<string> $arguments @return array{string, array<string, string|bool>} */
    public static function parse(array $arguments): array
    {
        $name = '';
        $options = [];
        foreach ($arguments as $argument) {
            if (!str_starts_with($argument, '--')) {
                if ($name !== '') {
                    throw new CliException('Only one component name may be provided.');
                }
                $name = $argument;
                continue;
            }
            [$key, $value] = array_pad(explode('=', substr($argument, 2), 2), 2, true);
            if ($key === '') {
                throw new CliException('CLI option name cannot be empty.');
            }
            $options[$key] = $value;
        }
        if ($name === '') {
            throw new CliException('A component name is required.');
        }

        return [$name, $options];
    }
}
