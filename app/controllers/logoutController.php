<?php 

class logoutController extends Controller {
  function __construct()
  {
  }

  function index()
  {
    if (!Auth::validate()) {
      Flasher::new('No hay una sesión iniciada, no podemos cerrarla.', 'danger');
      Redirect::to('login');
    }

    Auth::logout();
    Redirect::to('login');
  }
}