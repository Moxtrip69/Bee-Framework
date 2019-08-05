<?php 

class movimientosController extends Controller {
  function __construct()
  {
  }

  function index()
  {
    $data =
    [
      'title'   => 'Mis movimientos',
      'padding' => '0px'
    ];

    View::render('index', $data);
  }
}