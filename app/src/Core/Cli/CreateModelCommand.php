<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Creator\ComponentScaffolder;

final readonly class CreateModelCommand implements Command
{
    public function __construct(private ComponentScaffolder $creator)
    {
    }

    public function name(): string
    {
        return 'create:model';
    }

    public function description(): string
    {
        return 'Create a BeeModel class (--table, --fields, --no-timestamps).';
    }

    public function execute(array $arguments): int
    {
        [$name, $options] = CliOptions::parse($arguments);
        $fields = isset($options['fields'])
            ? $this->creator->parseFields(explode(',', (string) $options['fields']))
            : [];
        $result = $this->creator->createModel(
            $name,
            isset($options['table']) ? (string) $options['table'] : null,
            $fields,
            !isset($options['no-timestamps'])
        );
        echo sprintf('Created model %s at %s', $result['class'], $result['path']), PHP_EOL;

        return 0;
    }
}
