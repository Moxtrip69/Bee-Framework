<?php
/**
 * Plantilla general de controladores
 * @version 2.0.0
 *
 * Controlador de documentacion
 */
class documentacionController extends Controller implements ControllerInterface
{
  function __construct()
  {
    // Prevenir el ingreso si nos encontramos en producción y esta ruta es sólo para desarrollo o pruebas
    // if (!is_local()) {
    //   Redirect::to(DEFAULT_CONTROLLER);
    // }
    
    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //  Flasher::new('Debes iniciar sesión primero.', 'danger');
    //  Redirect::to('login');
    // }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index()
  {
    $this->setTitle('Documentación');
    $this->setView('index'); // por defecto es index
    $this->render();
  }

  function form_builder()
  {
    $this->setTitle('BeeFormBuilder');
    $this->setView('formBuilder');
    $this->render();
  }
}