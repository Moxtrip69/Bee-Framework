<?php 

class TableSchema
{
  private $sql        = null;
  private $table_name = null;
  private $column     = null;
  private $columns    = [];
  private $pk         = [];
  private $fk         = [];
  private $engine     = 'InnoDB'; // por defecto
  private $charset    = 'utf8'; // por defecto
  private $auto_inc   = 1; // por defecto
  private $ph         = '`%s`';

  public function __construct(String $table_name, String $engine = null, String $charset = null)
  {
    $this->table_name = $table_name;
    $this->engine     = $engine !== null ? $engine : $this->engine;
    $this->charset    = $charset !== null ? $charset : $this->charset;
  }

  public function add_column(String $column_name, String $type, $value = null, Bool $nulleable = true, $default_value = 'null', Bool $pk = false, Bool $auto_inc = false)
  {
    // type` varchar(30) DEFAULT NULL,
    $this->column = sprintf('%s %s %s %s', 
    sprintf($this->ph, $column_name),
    $this->validate_datatype($type, $value),
    $nulleable === true ? 'NULL' : 'NOT NULL',
    $this->validate_default_value($default_value)
    );

    // Si es primary key
    if ($pk === true) {
      $this->pk[]    = $column_name;
      $this->column .= sprintf(' PRIMARY KEY');
    }

    // Autoincrement
    if ($pk === true && $auto_inc === true) {
      $this->column .= sprintf(' AUTO_INCREMENT');
    }

    // Se anexa a la lista de columnas del query
    $this->columns[] = $this->column;

    return $this->columns;
  }

  private function validate_datatype($type, $value = null)
  {
    $output = '';

    switch (strtolower($type)) {
      case 'varchar':
        $default = 100;
        $min     = 1;
        $max     = 255;

        if (is_null($value)) {
          $value = $default;
        }

        if (!is_integer($value)) {
          throw new Exception(sprintf('El valor debe ser númerico y como máximo de %s.', $max));
        }

        $value  = $value > $max ? $max : ($value <= 0 ? $min : $value);
        $output = sprintf('%s(%s)', $type, $value);
        break;
      
      case 'int':
        $min     = 1;
        $max     = 11;

        if (!is_integer($value) && $value !== null) {
          throw new Exception(sprintf('El valor debe ser númerico y como máximo de %s.', $max));
        }

        $value  = $value > $max ? $max : ($value <= 0 ? $min : $value);
        $output = sprintf('%s(%s)', $type, $value);
        break;
      
      default:
        throw new Exception(sprintf('El tipo de valor ingresado %s no es válido.', $type));
        break;
    }

    return $output;
  }

  private function validate_default_value($default_value)
  {
    $output = '';
    
    switch ($default_value) {
      case false:
        $output = '';
        break;

      case 'null':
        $output = 'DEFAULT NULL';
        break;

      case 'current_time':
        $output = 'DEFAULT CURRENT_TIMESTAMP';
        break;

      case 'current_time_on_update':
        $output = 'DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        break;
      
      default:
        throw new Exception(sprintf('El valor por defecto %s no es válido.', $default_value));
        $output = sprintf('DEFAULT "%s"', $default_value);
        break;
    }

    return $output;
  }

  public function get($property)
  {
    if (!isset($this->{$property})) {
      throw new Exception(sprintf('No se reconoce la propiedad %s', $property));
    }

    return $this->{$property};
  }

  public function set($property, $value)
  {
    if (!isset($this->{$property})) {
      throw new Exception(sprintf('No se reconoce la propiedad %s', $property));
    }

    $this->{$property} = $value;

    return $this->{$property};
  }

  private function build()
  {
    if (empty($this->table_name)) {
      throw new Exception('Ingresa un nombre de tabla válido.');
    }

    if (empty($this->columns)) {
      throw new Exception('No hay columnas para crear la tabla.');
    }

    $this->sql = sprintf('CREATE TABLE %s', sprintf($this->ph, $this->table_name));
    $this->sql .= '(';

    // Agregando cada una de las columnas
    $total = count($this->columns);
    foreach ($this->columns as $i => $col) {
      if (($total - 1) === $i) {
        $this->sql .= sprintf('%s', $col);
      } else {
        $this->sql .= sprintf('%s,', $col);
      }
    }
    
    $this->sql .= ')';
    $this->sql .= sprintf(' ENGINE=%s AUTO_INCREMENT=%s DEFAULT CHARSET=%s;', $this->engine, $this->auto_inc, $this->charset);
    // 'CREATE TABLE `movements` (
    //   `id` int(10) NOT NULL AUTO_INCREMENT,
    //   `type` varchar(30) DEFAULT NULL,
    //   `description` varchar(255) DEFAULT NULL,
    //   `amount` float(10,2) DEFAULT NULL,
    //   `created_at` datetime DEFAULT NULL,
    //   `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //   PRIMARY KEY (`id`)
    // ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;'
    return $this;
  }

  public function get_sql()
  {
    $this->build();
    return $this->sql;
  }
}
