<?php 

class probarController extends Controller {
  function __construct()
  {
  }
  
  function index() {
    echo 'En '.__CLASS__;
  }

  function test()
  {
    echo 'Dentro del método Test de '.__CLASS__;
  }
}