<?php 

class errorController extends Controller {
  function __construct()
  {
    http_response_code(404);
  }
  
  function index() {
    $data =
    [
      'title' => 'Página no encontrada',
      'code'  => 404
    ];

    View::render('error', $data);
  }
}