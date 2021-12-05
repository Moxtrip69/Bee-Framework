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
  
  function test1()
  {
    debug(metaphone('Caballero'));
    debug(metaphone('Caballo'));
    debug(metaphone('Roberto'));
    die;
    try {

      $table_name = 'usuarios';
      //Model::drop($table_name);
      $table = new TableSchema($table_name);
      $table->add_column('id', 'int', 5, false, false, true, true);
      $table->add_column('nombre', 'varchar');
      $table->add_column('email', 'varchar');
      debug($table->get_sql());
      
      $res = Model::create($table);
      // debug($res);
      // var_dump($res);

    } catch (PDOException $e) {
      echo $e->getMessage();
    } catch (Exception $e) {
      echo 'Regular: '.$e->getMessage();
    }
    die;

    register_scripts(['unscript.css'], 'Comentario cool de estilos nuevos');

    $data = 
    [
      'title' => 'Reemplazar título',
      'msg'   => 'Bienvenido al controlador de "test", se ha creado con éxito si ves este mensaje.'
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }
}