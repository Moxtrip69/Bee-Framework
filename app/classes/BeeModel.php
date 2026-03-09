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

  /** =========================
   * Estado interno
   * ========================= */
  protected array $attributes = [];
  protected bool $exists      = false;

  // Cambio: nullable + lazy load
  protected ?PDO $pdo = null;

  /** =========================
   * Constructor
   * ========================= */
  public function __construct(array $attributes = [], bool $exists = false)
  {
    // Ya no dependemos de inicializar aquí obligatoriamente
    $this->fill($attributes);
    $this->exists = $exists;
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
      $this->attributes[$key] = $value;
    }
    return $this;
  }

  public function __get(string $key)
  {
    return $this->attributes[$key] ?? null;
  }

  public function __set(string $key, $value): void
  {
    $this->attributes[$key] = $value;
  }

  public function toArray(): array
  {
    return $this->attributes;
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
    $columns = array_keys($this->attributes);
    $fields  = implode(', ', $columns);
    $params  = implode(', ', array_map(fn($c) => ':' . $c, $columns));

    $sql     = "INSERT INTO {$this->table} ({$fields}) VALUES ({$params})";
    $stmt    = $this->pdo()->prepare($sql);

    $ok      = $stmt->execute($this->attributes);

    if ($ok) {
      if (count($this->primaryKeys) === 1 && !isset($this->attributes[$this->primaryKeys[0]])) {
        $this->attributes[$this->primaryKeys[0]] = $this->pdo()->lastInsertId();
      }
      $this->exists = true;
    }

    return $ok;
  }

  protected function update(): bool
  {
    $setColumns = array_diff(array_keys($this->attributes), $this->primaryKeys);

    $set   = implode(', ', array_map(fn($c) => "{$c} = :{$c}", $setColumns));
    $where = $this->buildWhere($this->primaryKeys);

    $sql   = "UPDATE {$this->table} SET {$set} WHERE {$where}";
    $stmt  = $this->pdo()->prepare($sql);

    // Parámetros solo necesarios
    $params = [];

    foreach ($setColumns as $col) {
      $params[$col] = $this->attributes[$col];
    }

    foreach ($this->primaryKeys as $pk) {
      $params[$pk] = $this->attributes[$pk];
    }

    return $stmt->execute($params);
  }

  public function delete(): bool
  {
    if (!$this->exists) {
      throw new Exception('No se puede eliminar un registro no persistido.');
    }

    $where = $this->buildWhere($this->primaryKeys);

    $sql   = "DELETE FROM {$this->table} WHERE {$where}";
    $stmt  = $this->pdo()->prepare($sql);

    // Solo PK
    $params = [];

    foreach ($this->primaryKeys as $pk) {
      $params[$pk] = $this->attributes[$pk];
    }

    return $stmt->execute($params);
  }

  /** =========================
   * Consultas clásicas
   * ========================= */

  public static function all(): array
  {
    $instance = new static;
    $sql      = "SELECT * FROM {$instance->table}";
    return $instance->pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function find(array $keys): ?static
  {
    $instance = new static;

    $where    = $instance->buildWhere(array_keys($keys));
    $sql      = "SELECT * FROM {$instance->table} WHERE {$where} LIMIT 1";

    $stmt     = $instance->pdo()->prepare($sql);
    $stmt->execute($keys);

    $row      = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? new static($row, true) : null;
  }

  public static function first(array $keys): ?static
  {
    $instance   = new static;
    $conditions = $instance->buildWhere(array_keys($keys));

    $sql        = "SELECT * FROM {$instance->table} WHERE {$conditions} LIMIT 1";
    $stmt       = $instance->pdo()->prepare($sql);
    $stmt->execute($keys);

    $row        = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? new static($row, true) : null;
  }

  public static function column(string $column, array $keys)
  {
    $instance   = new static;
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
    return implode(
      ' AND ',
      array_map(fn($c) => "{$c} = :{$c}", $columns)
    );
  }
}
