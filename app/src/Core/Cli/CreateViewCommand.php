<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Creator\ComponentScaffolder;

final readonly class CreateViewCommand implements Command
{
    public function __construct(private ComponentScaffolder $creator)
    {
    }

    public function name(): string
    {
        return 'create:view';
    }

    public function description(): string
    {
        return 'Create a view: create:view controller/view [--twig].';
    }

    public function execute(array $arguments): int
    {
        [$name, $options] = CliOptions::parse($arguments);
        $segments = explode('/', str_replace('\\', '/', $name));
        $view = array_pop($segments);
        $controller = implode('/', $segments);
        if ($controller === '' || $view === '') {
            throw new \InvalidArgumentException('View name must use controller/view format.');
        }
        $result = $this->creator->createView($controller, $view, isset($options['twig']) ? 'twig' : 'bee');
        echo sprintf('Created %s view at %s', $result['engine'], $result['path']), PHP_EOL;

        return 0;
    }
}
