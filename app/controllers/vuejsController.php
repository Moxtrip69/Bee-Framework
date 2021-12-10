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
    $data =
    [
      'title'   => 'Ejemplo administrador de tareas'
    ];

    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS.'vueApp.min.js'], 'Bee framework vuejs 3');

    View::render('index', $data);
  }
}