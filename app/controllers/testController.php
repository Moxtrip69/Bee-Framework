<?php

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de test
 */
class testController extends Controller {
  function __construct()
  {
    // Prevenir el ingreso en Producción
    if (!is_local()) {
      Redirect::to(DEFAULT_CONTROLLER);
    }
  }

  public function index()
  {
    debug($_SESSION);
  }
  
  public function db_user()
  {
    try {
      $sql   = 'SELECT * FROM pruebas';
      $db    = new Db();
      $conn  = $db->link();
    
      // begin the transaction
      $conn->beginTransaction();
  
      // our SQL statements
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('John')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Juan')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rigoberto')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rolon')");
    
      // commit the transaction
      $conn->commit();
      echo "New records created successfully";
    } catch(PDOException $e) {
      // roll back the transaction if something failed
      $conn->rollback();
      echo "Error: " . $e->getMessage();
    }
  }
  
  public function create_table()
  {
    try {
      // Si es requerido podemos hacer un drop table if exists
      // Model::drop($table_name); // Para borrar una tabla de la base de datos
      $table_name = 'usuarios';

      // Creamos un TableSchema
      $table      = new TableSchema($table_name);

      // Columnas de la tabla
      $table->add_column('id', 'int', 5, false, false, true, true);
      $table->add_column('nombre', 'varchar');
      $table->add_column('email', 'varchar');
      debug($table->get_sql());
      
      // Crea una tabla con base al TableSchema
      $res = Model::create($table);
      debug($res);

    } catch (PDOException $e) {
      echo $e->getMessage();
    } catch (Exception $e) {
      echo 'Regular: '.$e->getMessage();
    }
  }
}