<?php 

class errorController extends Controller {
  function __construct()
  {
    http_response_code(404);

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index() {
    $this->setTitle('Página no encontrada');
    $this->addToData('code', 404);
    $this->setView('error');
    $this->render();
  }
}