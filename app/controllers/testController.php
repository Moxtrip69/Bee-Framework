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
  
  function index()
  {
    register_scripts(['unscript.css'], 'Comentario cool de estilos nuevos');

    $data = 
    [
      'title' => 'Reemplazar título',
      'msg'   => 'Bienvenido al controlador de "test", se ha creado con éxito si ves este mensaje.'
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id)
  {
    View::render('ver');
  }

  function agregar()
  {
    View::render('agregar');
  }

  function post_agregar()
  {

  }

  function editar($id)
  {
    View::render('editar');
  }

  function post_editar()
  {

  }

  function borrar($id)
  {
    // Proceso de borrado
  }
}