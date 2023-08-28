<?php 

class homeController extends Controller implements ControllerInterface {
  function __construct()
  {
    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }

  function index()
  {
    $this->setTitle('Inicio');
    $this->setView('index');
    $this->render();
  }
}