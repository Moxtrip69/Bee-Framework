<?php
/**
 * Plantilla general de modelos
 * @version 1.1.8
 *
 * Modelo de user
 */
class userModel extends Model {
  /**
  * Nombre de la tabla
  */
  public static $t1 = 'bee_users';
  
  // Nombre de tablas secundarias
  //public static $t2 = '__tabla 2__'; 
  //public static $t3 = '__tabla 3__'; 

  // Esquema del Modelo
  

  function __construct()
  {
    // Constructor general
  }
  
  static function all()
  {
    // Todos los registros
    $sql = sprintf('SELECT * FROM %s ORDER BY id DESC', self::$t1);
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    // Todos los registros
    $sql = sprintf('SELECT * FROM %s ORDER BY id DESC', self::$t1);
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = sprintf('SELECT * FROM %s WHERE id = :id LIMIT 1', self::$t1);
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function update_by_id($id, $params)
  {
    return parent::update(self::$t1, ['id' => $id], $params);
  }

  static function delete_by_id($id)
  {
    return parent::remove(self::$t1, ['id' => $id]);
  }
}

