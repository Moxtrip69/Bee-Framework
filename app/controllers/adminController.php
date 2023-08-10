<?php
/**
 * Plantilla general de controladores
 * @version 1.0.5
 *
 * Controlador de admin
 */
class adminController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
  }
  
  function index()
  {
    register_scripts([JS . 'admin/demo.js'], 'Chartjs gráficas para administración');
    
    $data = 
    [
      'title'   => 'Reemplazar título',
      'buttons' =>
      [
        [
          'url'   => 'admin',
          'class' => 'btn-danger text-white',
          'id'    => '',
          'icon'  => 'fas fa-download',
          'text'  => 'Descargar'
        ],
        [
          'url'   => 'admin',
          'class' => 'btn-success text-white',
          'id'    => '',
          'icon'  => 'fas fa-file-pdf',
          'text'  => 'Exportar'
        ]
      ]
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function perfil()
  {
    $data =
    [
      'title' => 'Perfil de usuario',
      'user'  => User::profile()
    ];

    View::render('perfil', $data);
  }

  function botones()
  {
    $data =
    [
      'title' => 'Botones'
    ];
    
    View::render('botones', $data);
  }

  function cartas()
  {
    $data =
    [
      'title' => 'Cartas'
    ];
    
    View::render('cartas', $data);
  }

  function agregar()
  {
    $data = 
    [
      'title' => 'Reemplazar título'
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    // Proceso de agregado
  }

  function editar($id)
  {
    $data = 
    [
      'title' => 'Reemplazar título'
    ];

    View::render('editar', $data);
  }

  function post_editar()
  {
    // Proceso de actualizado
  }

  function borrar($id)
  {
    // Proceso de borrado
  }
}