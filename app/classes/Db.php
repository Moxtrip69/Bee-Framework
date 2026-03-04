<?php

/**
 * @author Joystick
 * 
 * Contribuido por
 * @author Pere
 * 
 * @version 1.1.0
 *
 */
class Db
{
  private static $instance = null; // Instancia para singleton
  private $link            = null;
  private $dsn;
  private $engine;
  private $host;
  private $name;
  private $charset;
  private $user;
  private $pass;
  private $options;

  /**
   * Constructor para nuestra clase
   */
  public function __construct()
  {
    $this->engine  = IS_LOCAL ? LDB_ENGINE : DB_ENGINE;
    $this->host    = IS_LOCAL ? LDB_HOST : DB_HOST;
    $this->name    = IS_LOCAL ? LDB_NAME : DB_NAME;
    $this->charset = IS_LOCAL ? LDB_CHARSET : DB_CHARSET;
    $this->dsn     = sprintf('%s:host=%s;dbname=%s;charset=%s', $this->engine, $this->host, $this->name, $this->charset);

    $this->user    = IS_LOCAL ? LDB_USER : DB_USER;
    $this->pass    = IS_LOCAL ? LDB_PASS : DB_PASS;

    $this->options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false
    ];

    return $this;
  }

  /**
   * Método para abrir una conexión a la base de datos
   * @param bool $throw_exception | para evitar die en error, en caso de no realizarse la conexión lanza excepción
   *
   * @return PDO|void
   */
  public static function connect(bool $throw_exception = false)
  {
    try {
      /**
       * Se implementó singleton para optimizar la carga de la base de datos y sus conexiones
       */
      if (self::$instance === null) {
        self::$instance = new self();
      }

      $self = self::$instance;

      if ($self->link !== null) return $self->link;

      $self->link = new PDO($self->dsn, $self->user, $self->pass, $self->options);

      return $self->link;
    } catch (PDOException $e) {
      if ($throw_exception === true) {
        throw new Exception($e->getMessage());
      }

      bee_db_die($e->getMessage()); // Muestra la vista especial para error de conexión
    }
  }

  /**
   * Método para hacer un query a la base de datos
   *
   * @param string $sql
   * @param array $params
   * @param integer $transaction
   * @return mixed
   */
  public static function query(string $sql, array $params = [], array $options = [])
  {
    $debug = $options['debug'] ?? false;
    $link  = self::connect();

    try {

      if ($debug) {
        logger(sprintf('DB Query: %s', $sql));
      }

      $stmt = $link->prepare($sql);
      $stmt->execute($params);

      // Detectar tipo real del statement (primer keyword)
      $statementType = strtoupper(
        strtok(trim($sql), " \n\t\r")
      );

      switch ($statementType) {

        case 'SELECT':
        case 'SHOW':
        case 'DESCRIBE':
        case 'PRAGMA':
          $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
          return !empty($results) ? $results : false;

        case 'INSERT':
          return $link->lastInsertId();

        case 'UPDATE':
        case 'DELETE':
          return true;
          // return $stmt->rowCount();
          // ⚠ Si quieres mantener 100% compatibilidad, cambia esto por:

        default:
          // ALTER, CREATE, DROP, TRUNCATE, etc.
          return true;
      }
    } catch (Throwable $e) {

      if ($debug) {
        logger(sprintf('DB Error: %s', $e->getMessage()));
      }

      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  /**
   * Método para hacer un query a la base de datos
   *
   * @param string $sql
   * @param array $params
   * @param integer $transaction
   * @return mixed
   */
  public static function querybu(string $sql, array $params = [], array $options = [])
  {
    $id          = null;
    $last_id     = false;
    $transaction = isset($options['transaction']) ? ($options['transaction'] === true ? true : false) : true;
    $debug       = isset($options['debug']) ? ($options['debug'] === true ? true : false) : false;
    $start       = isset($options['start']) ? ($options['start'] === true ? true : false) : false;
    $commit      = isset($options['commit']) ? ($options['commit'] === true ? true : false) : false;
    $rollback    = isset($options['rollback']) ? ($options['rollback'] === true ? true : false) : false;

    // Inicia conexión PDO
    $link  = self::connect();

    // Inicio de la transacción en caso de existir y estar abierta
    if (($transaction === true || $start === true) && !$link->inTransaction()) {
      $link->beginTransaction();
    }

    try {
      // Mostrar el query en el archivo de loggeo
      if ($debug) {
        logger(sprintf('DB Query: %s', $sql));
      }

      $query = $link->prepare($sql);
      $res   = $query->execute($params);

      // Manejando el tipo de query
      // SELECT | INSERT
      // SELECT * FROM usuarios;
      if (strpos($sql, 'SELECT') !== false) {

        return $query->rowCount() > 0 ? $query->fetchAll() : false; // no hay resultados

      } elseif (strpos($sql, 'INSERT') !== false) {

        $id      = $link->lastInsertId();
        $last_id = true;
      }

      // UPDATE | DELETE | ALTER TABLE | DROP TABLE | TRUNCATE | etc
      if (($transaction === true || $commit === true) && $link->inTransaction()) {
        $link->commit();
      }

      return $id !== null && $last_id === true ? $id : true;
    } catch (Exception $e) {
      if ($debug) {
        logger(sprintf('DB Error: %s', $e->getMessage()));
      }

      // Manejando errores en el query o la petición
      if (($transaction === true || $rollback === true) && $link->inTransaction()) {
        $link->rollBack();
      }

      throw new PDOException($e->getMessage());
    }
  }

  /**
   * Regresa la conexión a la base de datos
   *
   * @return PDO
   */
  public function link()
  {
    return self::connect();
  }

  /**
   * Iniciar transacción segura
   */
  public static function begin(): bool
  {
    $pdo = self::connect();

    if (!$pdo->inTransaction()) {
      return $pdo->beginTransaction();
    }

    return true; // ya había una activa
  }

  /**
   * Commit seguro
   */
  public static function commit(): bool
  {
    $pdo = self::connect();

    if ($pdo->inTransaction()) {
      return $pdo->commit();
    }

    return false; // no había transacción
  }

  /**
   * Rollback seguro
   */
  public static function rollBack(): bool
  {
    $pdo = self::connect();

    if ($pdo->inTransaction()) {
      return $pdo->rollBack();
    }

    return false; // no había transacción
  }

  /**
   * Saber si hay transacción activa
   */
  public static function inTransaction(): bool
  {
    return self::connect()->inTransaction();
  }
}
