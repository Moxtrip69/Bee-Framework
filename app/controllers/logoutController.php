<?php 

class logoutController extends Controller implements ControllerInterface {
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
    // Si las sesiones son persistentes es requerido borrar cookies
    if (persistent_session() === true) {
      BeeSession::destroy_session();
    }

    // Borrar toda la información de $_SESSION
    Auth::logout();
    Redirect::to('login');
  }
}