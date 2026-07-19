<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Creator\ComponentScaffolder;

final readonly class CreateControllerCommand implements Command
{
    public function __construct(private ComponentScaffolder $creator)
    {
    }

    public function name(): string
    {
        return 'create:controller';
    }

    public function description(): string
    {
        return 'Create a modern or legacy controller (--type, --view, --twig).';
    }

    public function execute(array $arguments): int
    {
        [$name, $options] = CliOptions::parse($arguments);
        $result = $this->creator->createController(
            $name,
            (string) ($options['type'] ?? 'modern'),
            isset($options['view']),
            isset($options['twig']) ? 'twig' : 'bee'
        );
        echo sprintf('Created %s controller %s at %s', $result['type'], $result['class'], $result['path']), PHP_EOL;

        return 0;
    }
}
