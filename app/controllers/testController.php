<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de test
 */
class testController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    /**
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
    */
  }

  public function index()
  {
    global $Bee_User;
    
    debug($Bee_User);
  }
  
  function create_table()
  {
    try {

      // Model::drop($table_name); // Para borrar una tabla de la base de datos
      $table_name = 'usuarios';
      $table = new TableSchema($table_name);
      $table->add_column('id', 'int', 5, false, false, true, true);
      $table->add_column('nombre', 'varchar');
      $table->add_column('email', 'varchar');
      debug($table->get_sql());
      
      $res = Model::create($table);
      debug($res);

    } catch (PDOException $e) {
      echo $e->getMessage();
    } catch (Exception $e) {
      echo 'Regular: '.$e->getMessage();
    }
  }
}