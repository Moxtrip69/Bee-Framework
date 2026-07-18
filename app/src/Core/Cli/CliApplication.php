<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Exception\CliException;

final class CliApplication
{
    /** @var array<string, Command> */
    private array $commands = [];

    public function add(Command $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    /** @param list<string> $arguments */
    public function run(array $arguments): int
    {
        $name = array_shift($arguments) ?? 'list';
        if ($name === 'list' || $name === '--help' || $name === '-h') {
            $this->renderCommandList();
            return 0;
        }

        if (!isset($this->commands[$name])) {
            throw new CliException(sprintf('Unknown command: %s.', $name));
        }

        return $this->commands[$name]->execute($arguments);
    }

    private function renderCommandList(): void
    {
        echo "Bee Framework CLI", PHP_EOL, PHP_EOL, "Available commands:", PHP_EOL;
        foreach ($this->commands as $command) {
            echo sprintf("  %-20s %s", $command->name(), $command->description()), PHP_EOL;
        }
    }
}
