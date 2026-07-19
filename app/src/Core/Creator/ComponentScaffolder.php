<?php

declare(strict_types=1);

namespace Bee\Core\Creator;

use Bee\Core\Creator\Exception\CreatorException;
use Bee\Core\Foundation\ApplicationRoot;

final readonly class ComponentScaffolder
{
    public function __construct(private ApplicationRoot $root)
    {
    }

    /** @return array{type: string, class: string, path: string, view?: string} */
    public function createController(
        string $name,
        string $type = 'modern',
        bool $withView = false,
        string $engine = 'bee'
    ): array {
        $type = strtolower($type);
        $engine = strtolower($engine);
        if (!in_array($type, ['legacy', 'modern'], true)) {
            throw new CreatorException('Controller type must be legacy or modern.');
        }
        if (!in_array($engine, ['bee', 'twig'], true)) {
            throw new CreatorException('View engine must be bee or twig.');
        }

        $modernStem = $this->classStem($name);
        $stem = $type === 'legacy' ? $this->snake($modernStem) : $modernStem;
        $viewDirectory = $this->snake($modernStem);

        $class = $stem . 'Controller';
        $target = $this->root->join('app', 'controllers', $class . '.php');
        $template = $this->template($type === 'modern' ? 'modernControllerTemplate.txt' : 'controllerTemplate.txt');
        $contents = str_replace(
            ['[[REPLACE]]', '[[CLASS]]', '[[ENGINE]]', '[[VIEW_DIRECTORY]]'],
            [$stem, $class, $engine, $viewDirectory],
            $template
        );
        $this->writeNewFile($target, $contents);

        $result = ['type' => $type, 'class' => $class, 'path' => $target];
        if ($withView) {
            try {
                $view = $this->createView($viewDirectory, 'index', $engine);
                $result['view'] = $view['path'];
            } catch (\Throwable $throwable) {
                @unlink($target);
                throw $throwable;
            }
        }

        return $result;
    }

    /** @return array{class: string, table: string, path: string} */
    public function createModel(
        string $name,
        ?string $table = null,
        array $fields = [],
        bool $timestamps = true
    ): array {
        $stem = $this->classStem($name);
        $class = $stem . 'Model';
        $tableName = $this->identifier($table ?: $this->snakePlural($stem), 'table');
        $normalizedFields = $this->normalizeFields($fields);
        $fillable = array_values(array_filter(
            array_keys($normalizedFields),
            static fn (string $field): bool => !in_array($field, ['id', 'created_at', 'updated_at'], true)
        ));
        $casts = array_filter($normalizedFields, static fn (string $cast): bool => $cast !== 'string');
        $template = $this->template('modernModelTemplate.txt');
        $contents = str_replace(
            ['[[CLASS]]', '[[TABLE]]', '[[FILLABLE]]', '[[CASTS]]', '[[TIMESTAMPS]]'],
            [
                $class,
                $tableName,
                $this->exportList($fillable, 8),
                $this->exportMap($casts, 8),
                $timestamps ? 'true' : 'false',
            ],
            $template
        );
        $target = $this->root->join('app', 'models', $class . '.php');
        $this->writeNewFile($target, $contents);

        return ['class' => $class, 'table' => $tableName, 'path' => $target];
    }

    /** @return array{controller: string, engine: string, path: string} */
    public function createView(string $controller, string $view, string $engine = 'bee'): array
    {
        $controller = $this->pathIdentifier($controller, 'controller');
        $view = $this->pathIdentifier($view, 'view');
        $engine = strtolower($engine);
        if (!in_array($engine, ['bee', 'twig'], true)) {
            throw new CreatorException('View engine must be bee or twig.');
        }

        $directory = $this->root->join('templates', 'views', ...explode('/', $controller));
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new CreatorException(sprintf('Unable to create view directory: %s.', $directory));
        }
        $extension = $engine === 'twig' ? 'twig' : 'php';
        $target = $directory . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $view) . 'View.' . $extension;
        $parent = dirname($target);
        if (!is_dir($parent) && !mkdir($parent, 0775, true) && !is_dir($parent)) {
            throw new CreatorException(sprintf('Unable to create nested view directory: %s.', $parent));
        }
        $template = $this->template($engine === 'twig' ? 'viewTwigTemplate.txt' : 'viewTemplate.txt');
        $this->writeNewFile($target, $template);

        return ['controller' => $controller, 'engine' => $engine, 'path' => $target];
    }

    /** @param list<string> $fields @return array<string, string> */
    public function parseFields(array $fields): array
    {
        $parsed = [];
        foreach ($fields as $definition) {
            if (trim($definition) === '') {
                continue;
            }
            [$name, $cast] = array_pad(explode(':', trim($definition), 2), 2, 'string');
            $parsed[$name] = $cast;
        }

        return $this->normalizeFields($parsed);
    }

    private function template(string $filename): string
    {
        $path = $this->root->join('templates', 'modules', 'bee', $filename);
        $contents = is_file($path) ? file_get_contents($path) : false;
        if (!is_string($contents)) {
            throw new CreatorException(sprintf('Creator template was not found: %s.', $filename));
        }

        return $contents;
    }

    private function writeNewFile(string $target, string $contents): void
    {
        if (is_file($target)) {
            throw new CreatorException(sprintf('File already exists: %s.', $target));
        }
        $handle = @fopen($target, 'x');
        if ($handle === false) {
            throw new CreatorException(sprintf('Unable to create file: %s.', $target));
        }
        try {
            if (fwrite($handle, $contents) !== strlen($contents)) {
                throw new CreatorException(sprintf('Unable to write complete file: %s.', $target));
            }
        } finally {
            fclose($handle);
        }
    }

    private function classStem(string $name): string
    {
        $name = preg_replace('/Controller$|Model$/i', '', trim($name)) ?? '';
        $words = preg_split('/[^A-Za-z0-9]+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($words === [] || preg_match('/^[A-Za-z]/', $words[0]) !== 1) {
            throw new CreatorException('Component name must start with a letter.');
        }
        $stem = lcfirst(implode('', array_map(static fn (string $word): string => ucfirst($word), $words)));

        return $this->identifier($stem, 'component');
    }

    private function identifier(string $value, string $type): string
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $value) !== 1) {
            throw new CreatorException(sprintf('Invalid %s name: %s.', $type, $value));
        }

        return $value;
    }

    private function pathIdentifier(string $value, string $type): string
    {
        $value = trim(str_replace('\\', '/', $value), '/');
        if (preg_match('#^[A-Za-z_][A-Za-z0-9_-]*(?:/[A-Za-z_][A-Za-z0-9_-]*)*$#D', $value) !== 1) {
            throw new CreatorException(sprintf('Invalid %s path: %s.', $type, $value));
        }

        return $value;
    }

    /** @param array<string, string>|list<string> $fields @return array<string, string> */
    private function normalizeFields(array $fields): array
    {
        $allowed = ['string', 'int', 'float', 'bool', 'array', 'json'];
        $normalized = [];
        foreach ($fields as $key => $value) {
            $name = is_int($key) ? (string) $value : (string) $key;
            $cast = is_int($key) ? 'string' : strtolower((string) $value);
            $name = $this->identifier(trim($name), 'field');
            if (!in_array($cast, $allowed, true)) {
                throw new CreatorException(sprintf('Unsupported cast for %s: %s.', $name, $cast));
            }
            $normalized[$name] = $cast;
        }

        return $normalized;
    }

    /** @param list<string> $values */
    private function exportList(array $values, int $spaces): string
    {
        if ($values === []) {
            return '';
        }
        $indent = str_repeat(' ', $spaces);

        return "\n" . $indent . implode("\n" . $indent, array_map(
            static fn (string $value): string => "'" . $value . "',",
            $values
        )) . "\n" . str_repeat(' ', max(0, $spaces - 4));
    }

    /** @param array<string, string> $values */
    private function exportMap(array $values, int $spaces): string
    {
        if ($values === []) {
            return '';
        }
        $indent = str_repeat(' ', $spaces);
        $items = [];
        foreach ($values as $key => $value) {
            $items[] = sprintf("'%s' => '%s',", $key, $value);
        }

        return "\n" . $indent . implode("\n" . $indent, $items)
            . "\n" . str_repeat(' ', max(0, $spaces - 4));
    }

    private function snakePlural(string $value): string
    {
        $snake = $this->snake($value);

        return str_ends_with($snake, 's') ? $snake : $snake . 's';
    }

    private function snake(string $value): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
