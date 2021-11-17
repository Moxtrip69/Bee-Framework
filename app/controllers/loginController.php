<?php 

class loginController extends Controller {
  function __construct()
  {
    if (Auth::validate()) {
      Flasher::new('Ya hay una sesión abierta.');
      Redirect::to('home/perfil');
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
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['usuario','csrf','password'], $_POST)) {
      Flasher::new('Acceso no autorizado.', 'danger');
      Redirect::back();
    }

    // Data pasada del formulario
    $usuario  = clean($_POST['usuario']);
    $password = clean($_POST['password']);

    // Información del usuario loggeado, simplemente se puede reemplazar aquí con un query a la base de datos
    // para cargar la información del usuario si es existente
    $user = 
    [
      'id'       => 123,
      'name'     => 'Bee Default', 
      'email'    => 'hellow@joystick.com.mx', 
      'avatar'   => 'myavatar.jpg', 
      'tel'      => '11223344', 
      'color'    => '#112233',
      'user'     => 'bee', // puedes cambiar este dato a lo que gustes si usarás este sistema de login (es relativamente seguro dependiendo el tipo de sistema)
      'password' => '$2y$10$tV0XLhk.v8JBcqIjPhkFcemUjASG8Bt3ggDTnzV5VYkluoAc5.sAC' // puedes generar una nueva en bee/password
    ];


    if ($usuario !== $user['user'] || !password_verify($password.AUTH_SALT, $user['password'])) {
      Flasher::new('Las credenciales no son correctas.', 'danger');
      Redirect::back();
    }

    // Loggear al usuario
    Auth::login($user['id'], $user);
    Redirect::to('home/perfil');
  }
}