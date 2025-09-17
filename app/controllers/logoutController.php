<?php 

class logoutController extends Controller implements ControllerInterface
{
  function __construct()
  {
    // Validar la sesión del usuario
    if (!Auth::validate()) {
      Flasher::new('No hay una sesión iniciada, no podemos cerrarla.', 'danger');
      Redirect::to('login');
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }

  function index()
  {
    // Destruir la sesión de la DB y borrar cookies
    BeeSession::destroy_session();

    // Borrar toda la información de $_SESSION
    session_destroy();

    Redirect::to('login');
  }
}