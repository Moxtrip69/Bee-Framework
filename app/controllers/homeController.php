<?php 

class homeController extends Controller {
  function __construct()
  {
  }

  function index()
  {
    $data =
    [
      'title' => 'Home'
    ];

    View::render('index', $data);
  }
}