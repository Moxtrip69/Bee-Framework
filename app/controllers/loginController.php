<?php 

class loginController extends Controller {
  function __construct()
  {
    if (Auth::validate()) {
      Flasher::new('Ya hay una sesión abierta.');
      Redirect::to('home/flash');
    }
  }

  function index()
  {
    $data =
    [
      'title'   => 'Ingresar a tu cuenta',
      'padding' => '0px'
    ];

    View::render('index', $data);
  }

  function post_login()
  {
    if (!Csrf::validate($_POST['csrf'])) {
      Flasher::new('Acceso no autorizado.', 'danger');
      Redirect::back();
    }

    $def_id       = 123;
    $def_user     = 'bee';
    $def_password = '123456';

    if (clean($_POST['usuario']) !== $def_user || clean($_POST['password']) !== $def_password) {
      Flasher::new('Las credenciales no son correctas.', 'danger');
      Redirect::back();
    }

    // Loggear al usuario
    Auth::login($def_id, ['name' => 'Bee Joystick', 'email' => 'hellow@joystick.com.mx', 'avatar' => 'myavatar.jpg', 'tel' => '11223344', 'color' => '#112233']);
    Redirect::to('home/flash');
  }
}