<?php 

class Db
{
  private $link = null;
  private $engine;
  private $host;
  private $name;
  private $user;
  private $pass;
  private $charset;
  private $options;
  
  /**
   * Constructor para nuestra clase
   */
  public function __construct()
  {
    $this->engine  = IS_LOCAL ? LDB_ENGINE : DB_ENGINE;
    $this->name    = IS_LOCAL ? LDB_NAME : DB_NAME;
    $this->user    = IS_LOCAL ? LDB_USER : DB_USER;
    $this->pass    = IS_LOCAL ? LDB_PASS : DB_PASS;
    $this->charset = IS_LOCAL ? LDB_CHARSET : DB_CHARSET;
    $this->host    = IS_LOCAL ? LDB_HOST : DB_HOST;
    $this->options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false
		];

    return $this;    
  }

  /**
   * Método para abrir una conexión a la base de datos
   *
   * @return mixed
   */
  private static function connect() 
  {
    try {
      $self       = new self();
      if ($self->link !== null) return $self->link;
      $self->link = new PDO($self->engine.':host='.$self->host.';dbname='.$self->name.';charset='.$self->charset, $self->user, $self->pass, $self->options);
      return $self->link;
    } catch (PDOException $e) {
      die(sprintf('No  hay conexión a la base de datos, hubo un error: %s', $e->getMessage()));
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
  public static function query($sql, $params = [], $options = [])
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

    // Inicio de la transacción
    if ($transaction === true || $start === true) {
      $link->beginTransaction();
    }

    try {
      $query = $link->prepare($sql);
      $res   = $query->execute($params);
  
      // Manejando el tipo de query
      // SELECT | INSERT
      // SELECT * FROM usuarios;
      if(strpos($sql, 'SELECT') !== false) {
        
        return $query->rowCount() > 0 ? $query->fetchAll() : false; // no hay resultados
  
      } elseif(strpos($sql, 'INSERT') !== false) {
  
        $id      = $link->lastInsertId();
        $last_id = true;
  
      }

      // UPDATE | DELETE | ALTER TABLE | DROP TABLE | TRUNCATE | etc
      if ($transaction === true || $commit === true) {
        $link->commit();
      }

      return $id !== null && $last_id === true ? $id : true;
        
    } catch (Exception $e) {
      if ($debug === true) {
        logger(sprintf('DB Error: %s', $e->getMessage()));
      }

      // Manejando errores en el query o la petición
      if ($transaction === true || $rollback === true) {
        $link->rollBack();
      }

      throw new PDOException($e->getMessage());
    }
  }
}