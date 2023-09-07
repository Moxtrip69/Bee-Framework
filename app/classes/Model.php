<?php 

/**
 * Modelo principal y general
 * 
 * @version 1.0.5
 */
class Model extends Db {
  /**
   * Lista registros de la base de datos o un solo registro,
	 * regresa un array de resultados, si el $limit es 1, regresa sólo un array
   *
   * @param string $table
   * @param array $params
   * @param integer $limit
   * @return mixed
   */
	public static function list(string $table, array $params = [], ?int $limit = null)
	{	
		// It creates the col names and values to bind
		$cols_values = "";
		$limits      = "";
		
		if (!empty($params)) {
			$cols_values .= "WHERE";
			foreach ($params as $key => $value) {
				$cols_values .= " {$key} = :{$key} AND";
			}
			$cols_values = substr($cols_values, 0 , -3);
		}

		// If $limit is set, set a limit of data read
		if ($limit !== null) {
			$limits = " LIMIT {$limit}";
		}

		// Query creation
		$stmt = "SELECT * FROM $table {$cols_values}{$limits}";

		// Calling DB and querying
		if (!$rows = parent::query($stmt , $params)) {
      return false;
		}

    return $limit === 1 ? $rows[0] : $rows;
  }
  
  /**
	* Add a new record to DB
	* @access public
	* @var string | array
	* @return bool
	**/
	public static function add(string $table, array $params)
	{	
		$cols         = "";
		$placeholders = "";
		
		foreach ($params as $key => $value) {
			$cols         .= "{$key} ,";
			$placeholders .= ":{$key} ,";
		}

		$cols         = substr($cols, 0 , -1);
		$placeholders = substr($placeholders, 0 , -1);
		$stmt         = 
		"INSERT INTO {$table}
		({$cols})
		VALUES
		({$placeholders})
		";
		
		// Manda el statement a query()
		if ($id = parent::query($stmt , $params)) {
			return $id;
		}

		return false;
  }
  
  /**
	* Add a new record to DB
	* @access public
	* @var string | array
	* @return bool
	**/
	public static function update(string $table, array $haystack = [] , array $params = [])
	{	
		$placeholders = "";
		$col          = "";

		foreach ($params as $key => $value) {
			$placeholders .= " {$key} = :{$key} ,";
		}
		$placeholders = substr($placeholders, 0 , -1);

		if(count($haystack) > 1){
			foreach ($haystack as $key => $value) {
				$col .= " $key = :$key AND";
			}

			$col = substr($col, 0, -3);

		} else {
			foreach ($haystack as $key => $value) {
				$col .= " $key = :$key";
			}
		}

		$stmt = "UPDATE $table SET $placeholders WHERE $col";

		// Manda el statement a query()
		if (!parent::query($stmt , array_merge($params,$haystack))) {
      return false;
		}
    
    return true;
  }
  
  /**
   * Borra un registro de la base de datos
	 * Se cambió $limite = 1 por defecto a $limite = null esto porque generaba problemas
	 * al realizar borrado de muchos registros y se veia obligago a colocar un finito
	 * para que más de 1 registro se borrara simultaneamente.
   *
   * @param string $table
   * @param array $params
   * @param integer $limit
   * @return void
   */
  public static function remove(string $table, array $params = [], ?int $limit = null)
	{	
		// It creates the col names and values to bind
		$cols_values = "";
		$limits      = "";

		if (!empty($params)) {
			$cols_values .= "WHERE";
			foreach ($params as $key => $value) {
				$cols_values .= " {$key} = :{$key} AND";
			}
			$cols_values = substr($cols_values, 0 , -3);
		}

		// If $limit is set, set a limit of data read
		if ($limit !== null && is_integer($limit)) {
			$limits = " LIMIT {$limit}";
		}

		// Query creation
		$stmt = "DELETE FROM $table {$cols_values}{$limits}";

		// Calling DB and querying
		if (!parent::query($stmt , $params)) {
      return false;
		}
    
    return true;
	}

	/**
	 * Elimina o hace drop de una tabla
	 * pasando el segundo parámetro en false podrá regresar una excepción si no existe la tabla
	 * de lo contrario siempre será true la respuesta
	 *
	 * @param string $table
	 * @param boolean $if_exists
	 * @return bool
	 */
	public static function dropTable(string $table, bool $if_exists = true)
	{
		$sql = sprintf('DROP TABLE %s %s', $if_exists === true ? 'IF EXISTS' : null, $table);
		return parent::query($sql, [], ['transaction' => false]);
	}

	/**
	 * Remueve todos los registros de una tabla
	 * reiniciando su esquema a 0
	 *
	 * @param string $table
	 * @return bool
	 */
	public static function truncateTable(string $table)
	{
		$sql = sprintf('TRUNCATE TABLE %s', $table);
		return parent::query($sql, [], ['transaction' => false]);
	}

	/**
	 * Crea una nueva tabla de la base de datos actualmente
	 * conectada.
	 *
	 * @param TableSchema $schema el query SQL de la creación de la tabla
	 * @return bool
	 */
	public static function createTable(TableSchema $schema)
	{
		if (self::table_exists($schema->getTableName())) {
			throw new Exception(sprintf('La tabla %s ya existe.', $schema->getTableName()));
		}
		
		$sql = $schema->get_sql();
		return parent::query($sql, [], ['transaction' => false]);
	}

	/**
	 * Regresa el listado de tablas
	 * existentes en nuestra base de datos
	 * actualmente conectada
	 *
	 * @return array
	 */
	public static function list_tables()
	{
		try {
			// Incializamos nuestra conexión a la base de datos
			$tables = [];
			$sql    = 'SHOW TABLES';
			$db     = parent::connect();
			
			// Preparando nuestra petición
			$stm = $db->prepare($sql);
	
			// Ejecutando nuestra petición
			$stm->execute();
	
			// Listamos las filas encontradas (tablas)
			$res = $stm->fetchAll(PDO::FETCH_NUM);

			if (empty($res)) {
				return []; // No existen tablas en la base de datos
			}
	
			// Vamos a formar nuestro array de tablas
			foreach($res as $table){
				$tables[] = $table[0];
			}

			// Regresamos el listado de nuestras tablas
			return $tables;

		} catch (PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Verificamos si existe una tabla en específico
	 * dentro del modelo de nuestra base de datos
	 *
	 * @param string $table
	 * @return bool
	 */
	public static function table_exists(string $table)
	{
		$tables = self::list_tables();

		if (empty($tables)) return false;

		// Vemos si existe la tabla que buscamos dentro del array de tablas que nos regresa
		// nuestra base de datos
		return in_array($table, $tables);
	}
}