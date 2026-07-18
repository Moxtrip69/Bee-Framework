<?php

/**
 * Modelo principal y nuevo de Bee Framework
 * 
 * @since 1.6.0
 * @version 1.0.1
 */
abstract class BeeModel
{
  /** =========================
   * Configuración del modelo
   * ========================= */
  protected string $table;
  protected array $primaryKeys = ['id'];
  protected array $fillable = [];
  protected array $guarded = [];
  protected array $casts = [];
  protected bool $timestamps = false;

  /** =========================
   * Estado interno
   * ========================= */
  protected array $attributes = [];
  protected array $original   = [];
  protected bool $exists      = false;

  // Cambio: nullable + lazy load
  protected ?PDO $pdo = null;

  /** =========================
   * Constructor
   * ========================= */
  public function __construct(array $attributes = [], bool $exists = false)
  {
    // Ya no dependemos de inicializar aquí obligatoriamente
	$exists ? $this->forceFill($attributes) : $this->fill($attributes);
    $this->exists = $exists;
    $this->syncOriginal();
  }

  /** =========================
   * PDO Lazy Loader
   * ========================= */
  protected function pdo(): PDO
  {
    if ($this->pdo === null) {
      $this->pdo = Db::connect();
    }

    return $this->pdo;
  }

  /** =========================
   * Manejo de atributos
   * ========================= */
  public function fill(array $data): self
  {
    foreach ($data as $key => $value) {
	  if ($this->isFillable((string) $key)) {
		$this->attributes[(string) $key] = $value;
	  }
    }
    return $this;
  }

  public function forceFill(array $data): self
  {
	foreach ($data as $key => $value) {
	  $this->attributes[(string) $key] = $value;
	}

	return $this;
  }

  public function __get(string $key)
  {
	$value = $this->attributes[$key] ?? null;

	return $this->castAttribute($key, $value);
  }

  public function __set(string $key, $value): void
  {
    $this->attributes[$key] = $value;
  }

  public function __isset(string $key): bool
  {
	return isset($this->attributes[$key]);
  }

  public function toArray(): array
  {
	$output = [];
	foreach ($this->attributes as $key => $value) {
	  $output[$key] = $this->castAttribute($key, $value);
	}

	return $output;
  }

  public function isDirty(?string $key = null): bool
  {
	if ($key !== null) {
	  return !array_key_exists($key, $this->original)
		|| $this->original[$key] !== ($this->attributes[$key] ?? null);
	}

	return $this->dirtyAttributes() !== [];
  }

  public function exists(): bool
  {
	return $this->exists;
  }

  public function setConnection(PDO $pdo): self
  {
	$this->pdo = $pdo;

	return $this;
  }

  /** =========================
   * Transacciones (POO)
   * ========================= */
  public function beginTransaction(): self
  {
    if (!$this->pdo()->inTransaction()) {
      $this->pdo()->beginTransaction();
    }
    return $this;
  }

  public function commit(): void
  {
    if ($this->pdo()->inTransaction()) {
      $this->pdo()->commit();
    }
  }

  public function rollBack(): void
  {
    if ($this->pdo()->inTransaction()) {
      $this->pdo()->rollBack();
    }
  }

  /** =========================
   * Persistencia
   * ========================= */
  public function save(): bool
  {
    return $this->exists ? $this->update() : $this->insert();
  }

  protected function insert(): bool
  {
	if ($this->attributes === []) {
	  throw new InvalidArgumentException('No hay atributos para insertar.');
	}
	$this->applyCreatedTimestamp();
    $columns = array_keys($this->attributes);
	$this->validateIdentifiers($columns);
    $fields  = implode(', ', $columns);
    $params  = implode(', ', array_map(fn($c) => ':' . $c, $columns));

    $sql     = "INSERT INTO {$this->table} ({$fields}) VALUES ({$params})";
    $stmt    = $this->pdo()->prepare($sql);

	$params = [];
	foreach ($this->attributes as $column => $value) {
	  $params[$column] = $this->databaseValue($column, $value);
	}
	$ok = $stmt->execute($params);

    if ($ok) {
      if (count($this->primaryKeys) === 1 && !isset($this->attributes[$this->primaryKeys[0]])) {
        $this->attributes[$this->primaryKeys[0]] = $this->pdo()->lastInsertId();
      }
      $this->exists = true;
	  $this->syncOriginal();
    }

    return $ok;
  }

  protected function update(): bool
  {
	$this->ensurePrimaryKeysArePresent();
	$this->applyUpdatedTimestamp();
	$dirty = $this->dirtyAttributes();
	$setColumns = array_diff(array_keys($dirty), $this->primaryKeys);
	if ($setColumns === []) {
	  return true;
	}
	$this->validateIdentifiers([...$setColumns, ...$this->primaryKeys]);

    $set   = implode(', ', array_map(fn($c) => "{$c} = :{$c}", $setColumns));
    $where = $this->buildWhere($this->primaryKeys);

    $sql   = "UPDATE {$this->table} SET {$set} WHERE {$where}";
    $stmt  = $this->pdo()->prepare($sql);

    // Parámetros solo necesarios
    $params = [];

    foreach ($setColumns as $col) {
	  $params[$col] = $this->databaseValue($col, $this->attributes[$col]);
    }

    foreach ($this->primaryKeys as $pk) {
      $params[$pk] = $this->attributes[$pk];
    }

	$updated = $stmt->execute($params);
	if ($updated) {
	  $this->syncOriginal();
	}

	return $updated;
  }

  public function delete(): bool
  {
    if (!$this->exists) {
      throw new Exception('No se puede eliminar un registro no persistido.');
    }
	$this->ensurePrimaryKeysArePresent();

    $where = $this->buildWhere($this->primaryKeys);

    $sql   = "DELETE FROM {$this->table} WHERE {$where}";
    $stmt  = $this->pdo()->prepare($sql);

    // Solo PK
    $params = [];

    foreach ($this->primaryKeys as $pk) {
      $params[$pk] = $this->attributes[$pk];
    }

	$deleted = $stmt->execute($params);
	if ($deleted) {
	  $this->exists = false;
	}

	return $deleted;
  }

  /** =========================
   * Consultas clásicas
   * ========================= */

  public static function fetchAll(): array
  {
    $instance = new static;
    $sql      = "SELECT * FROM {$instance->table}";
    return $instance->pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function all(): array
  {
	return static::newQuery()->get();
  }

  public static function create(array $attributes): static
  {
	$model = new static($attributes);
	if (!$model->save()) {
	  throw new RuntimeException('No fue posible crear el modelo.');
	}

	return $model;
  }

  public static function newQuery(): \Bee\Core\Database\ModelQuery
  {
	$instance = new static;
	$instance->validateIdentifiers([$instance->table]);

	return new \Bee\Core\Database\ModelQuery($instance->pdo(), static::class, $instance->table);
  }

  public static function where(string $column, mixed $operatorOrValue, mixed $value = null): \Bee\Core\Database\ModelQuery
  {
	return func_num_args() === 2
	  ? static::newQuery()->where($column, $operatorOrValue)
	  : static::newQuery()->where($column, $operatorOrValue, $value);
  }

  public static function whereIn(string $column, array $values): \Bee\Core\Database\ModelQuery
  {
	return static::newQuery()->whereIn($column, $values);
  }

  public static function whereNull(string $column): \Bee\Core\Database\ModelQuery
  {
	return static::newQuery()->whereNull($column);
  }

  public static function orderBy(string $column, string $direction = 'asc'): \Bee\Core\Database\ModelQuery
  {
	return static::newQuery()->orderBy($column, $direction);
  }

  public static function find(array|string|int $keys): ?static
  {
    $instance = new static;
	if (!is_array($keys)) {
	  if (count($instance->primaryKeys) !== 1) {
		throw new InvalidArgumentException('Los modelos con llave compuesta requieren un array.');
	  }
	  $keys = [$instance->primaryKeys[0] => $keys];
	}
	if ($keys === []) {
	  return null;
	}
	$instance->validateIdentifiers(array_keys($keys));
    $where    = $instance->buildWhere(array_keys($keys));

    $sql      = "SELECT * FROM {$instance->table} WHERE {$where} LIMIT 1";

    $stmt     = $instance->pdo()->prepare($sql);
    $stmt->execute($keys);

    $row      = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$row) {
	  return null;
	}
	$model = new static($row, true);
	$model->setConnection($instance->pdo());

	return $model;
  }

  public static function first(array $keys): ?static
  {
    $instance   = new static;
    $conditions = $instance->buildWhere(array_keys($keys));

    $sql        = "SELECT * FROM {$instance->table} WHERE {$conditions} LIMIT 1";

    $stmt       = $instance->pdo()->prepare($sql);
    $stmt->execute($keys);

    $row        = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$row) {
	  return null;
	}
	$model = new static($row, true);
	$model->setConnection($instance->pdo());

	return $model;
  }

  public static function column(string $column, array $keys)
  {
    $instance   = new static;
	$instance->validateIdentifiers([$column, ...array_keys($keys)]);
    $conditions = $instance->buildWhere(array_keys($keys));

    $sql        = "SELECT {$column} FROM {$instance->table} WHERE {$conditions} LIMIT 1";

    $stmt       = $instance->pdo()->prepare($sql);
    $stmt->execute($keys);

    return $stmt->fetchColumn();
  }

  public static function query(string $sql, array $params = []): PDOStatement
  {
    $instance = new static;

    $stmt     = $instance->pdo()->prepare($sql);
    $stmt->execute($params);
    
    return $stmt;
  }

  /** =========================
   * Helpers internos
   * ========================= */
  protected function buildWhere(array $columns): string
  {
	$this->validateIdentifiers($columns);
    return implode(
      ' AND ',
      array_map(fn($c) => "{$c} = :{$c}", $columns)
    );
  }

  protected function isFillable(string $key): bool
  {
	if ($this->fillable !== []) {
	  return in_array($key, $this->fillable, true);
	}

	return !in_array($key, $this->guarded, true);
  }

  protected function castAttribute(string $key, mixed $value): mixed
  {
	if ($value === null || !isset($this->casts[$key])) {
	  return $value;
	}

	return match ($this->casts[$key]) {
	  'int', 'integer' => (int) $value,
	  'float', 'double' => (float) $value,
	  'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
	  'string' => (string) $value,
	  'array', 'json' => is_array($value) ? $value : json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR),
	  default => $value,
	};
  }

  protected function databaseValue(string $key, mixed $value): mixed
  {
	if ($value === null || !isset($this->casts[$key])) {
	  return $value;
	}

	return match ($this->casts[$key]) {
	  'array', 'json' => is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR),
	  'bool', 'boolean' => $value ? 1 : 0,
	  default => $value,
	};
  }

  protected function dirtyAttributes(): array
  {
	return array_filter(
	  $this->attributes,
	  fn (mixed $value, string $key): bool => !array_key_exists($key, $this->original)
		|| $this->original[$key] !== $value,
	  ARRAY_FILTER_USE_BOTH
	);
  }

  protected function syncOriginal(): void
  {
	$this->original = $this->attributes;
  }

  protected function ensurePrimaryKeysArePresent(): void
  {
	foreach ($this->primaryKeys as $primaryKey) {
	  if (!array_key_exists($primaryKey, $this->attributes)) {
		throw new LogicException('Falta la llave primaria requerida: ' . $primaryKey);
	  }
	}
  }

  protected function applyCreatedTimestamp(): void
  {
	if ($this->timestamps) {
	  $now = date('Y-m-d H:i:s');
	  $this->attributes['created_at'] ??= $now;
	  $this->attributes['updated_at'] ??= $now;
	}
  }

  protected function applyUpdatedTimestamp(): void
  {
	if ($this->timestamps) {
	  $this->attributes['updated_at'] = date('Y-m-d H:i:s');
	}
  }

  protected function validateIdentifiers(array $identifiers): void
  {
	foreach ($identifiers as $identifier) {
	  if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', (string) $identifier) !== 1) {
		throw new InvalidArgumentException('Identificador SQL no seguro: ' . (string) $identifier);
	  }
	}
  }
}
