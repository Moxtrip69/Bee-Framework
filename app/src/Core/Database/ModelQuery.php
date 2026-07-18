<?php

declare(strict_types=1);

namespace Bee\Core\Database;

use InvalidArgumentException;
use PDO;

final class ModelQuery
{
    /** @var list<string> */
    private array $columns = ['*'];

    /** @var list<string> */
    private array $wheres = [];

    /** @var array<string, mixed> */
    private array $bindings = [];

    /** @var list<string> */
    private array $orders = [];

    private ?int $limitValue = null;

    private ?int $offsetValue = null;

    private int $bindingIndex = 0;

    /** @param class-string $modelClass */
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $modelClass,
        private readonly string $table
    ) {
        $this->identifier($table);
    }

    public function select(string ...$columns): self
    {
        $clone = clone $this;
        $clone->columns = $columns === [] ? ['*'] : array_map($this->identifier(...), $columns);

        return $clone;
    }

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): self
    {
        $clone = clone $this;
        $column = $clone->identifier($column);
        $operator = func_num_args() === 2 ? '=' : strtoupper(trim((string) $operatorOrValue));
        $value = func_num_args() === 2 ? $operatorOrValue : $value;
        if (!in_array($operator, ['=', '!=', '<>', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'], true)) {
            throw new InvalidArgumentException('Unsupported query operator: ' . $operator);
        }
        $placeholder = $clone->bind($value);
        $clone->wheres[] = sprintf('%s %s %s', $column, $operator, $placeholder);

        return $clone;
    }

    /** @param list<mixed> $values */
    public function whereIn(string $column, array $values): self
    {
        if ($values === []) {
            $clone = clone $this;
            $clone->wheres[] = '1 = 0';
            return $clone;
        }
        $clone = clone $this;
        $column = $clone->identifier($column);
        $placeholders = array_map(fn (mixed $value): string => $clone->bind($value), array_values($values));
        $clone->wheres[] = sprintf('%s IN (%s)', $column, implode(', ', $placeholders));

        return $clone;
    }

    public function whereNull(string $column): self
    {
        $clone = clone $this;
        $clone->wheres[] = $clone->identifier($column) . ' IS NULL';

        return $clone;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException('Order direction must be ASC or DESC.');
        }
        $clone = clone $this;
        $clone->orders[] = $clone->identifier($column) . ' ' . $direction;

        return $clone;
    }

    public function limit(int $limit): self
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Query limit must be greater than zero.');
        }
        $clone = clone $this;
        $clone->limitValue = $limit;

        return $clone;
    }

    public function offset(int $offset): self
    {
        if ($offset < 0) {
            throw new InvalidArgumentException('Query offset cannot be negative.');
        }
        $clone = clone $this;
        $clone->offsetValue = $offset;

        return $clone;
    }

    /** @return list<object> */
    public function get(): array
    {
        $statement = $this->pdo->prepare($this->selectSql());
        $statement->execute($this->bindings);

        return array_map(function (array $row): object {
            $model = new $this->modelClass($row, true);
            $model->setConnection($this->pdo);

            return $model;
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function first(): ?object
    {
        $models = $this->limit(1)->get();

        return $models[0] ?? null;
    }

    public function value(string $column): mixed
    {
        $model = $this->select($column)->first();

        return $model?->{$column};
    }

    public function count(): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM ' . $this->table . $this->whereSql());
        $statement->execute($this->bindings);

        return (int) $statement->fetchColumn();
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /** @return array{data: list<object>, total: int, per_page: int, current_page: int, last_page: int} */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $perPage = max(1, $perPage);
        $page = max(1, $page);
        $total = $this->count();

        return [
            'data' => $this->limit($perPage)->offset(($page - 1) * $perPage)->get(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /** @param array<string, mixed> $values */
    public function update(array $values): bool
    {
        if ($values === [] || $this->wheres === []) {
            throw new InvalidArgumentException('A constrained update requires values and at least one WHERE clause.');
        }
        $clone = clone $this;
        $sets = [];
        foreach ($values as $column => $value) {
            $sets[] = $clone->identifier((string) $column) . ' = ' . $clone->bind($value);
        }
        $statement = $clone->pdo->prepare('UPDATE ' . $clone->table . ' SET ' . implode(', ', $sets) . $clone->whereSql());

        return $statement->execute($clone->bindings);
    }

    public function delete(): bool
    {
        if ($this->wheres === []) {
            throw new InvalidArgumentException('A bulk delete requires at least one WHERE clause.');
        }
        $statement = $this->pdo->prepare('DELETE FROM ' . $this->table . $this->whereSql());

        return $statement->execute($this->bindings);
    }

    private function selectSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table . $this->whereSql();
        if ($this->orders !== []) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }
        if ($this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
        }
        if ($this->offsetValue !== null) {
            $sql .= ' OFFSET ' . $this->offsetValue;
        }

        return $sql;
    }

    private function whereSql(): string
    {
        return $this->wheres === [] ? '' : ' WHERE ' . implode(' AND ', $this->wheres);
    }

    private function bind(mixed $value): string
    {
        $placeholder = ':bee_' . $this->bindingIndex++;
        $this->bindings[$placeholder] = $value;

        return $placeholder;
    }

    private function identifier(string $identifier): string
    {
        if ($identifier !== '*' && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $identifier) !== 1) {
            throw new InvalidArgumentException('Unsafe SQL identifier: ' . $identifier);
        }

        return $identifier;
    }
}
