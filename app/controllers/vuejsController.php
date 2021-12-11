<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de vuejs
 */
class vuejsController extends Controller {
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
  
  function index()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS.'vueApp.min.js'], 'Bee framework vuejs 3');

    $data =
    [
      'title'   => 'Ejemplo administrador de tareas'
    ];

    View::render('index', $data);
  }

  function test()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS.'vueApp.min.js'], 'Bee framework vuejs 3');

    $data =
    [
      'title' => 'Componente de prueba'
    ];

    View::render('test', $data);
  }
}