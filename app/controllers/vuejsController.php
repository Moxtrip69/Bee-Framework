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
    // if (!Auth::validate()) {
    //   Flasher::new('Debes iniciar sesión primero.', 'danger');
    //   Redirect::to('login');
    // }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS.'vueApp.min.js'], 'Bee framework vuejs 3');

    $this->setTitle('Ejemplo de administrador de tareas');
    $this->setView('index');
    $this->render();
  }

  function test()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS.'vueApp.min.js'], 'Bee framework vuejs 3');

    $this->setTitle('Componente de prueba');
    $this->setView('test');
    $this->render();
  }
}