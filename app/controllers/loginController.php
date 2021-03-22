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
      'user'     => 'bee',
      'password' => '$2y$10$R18ASm3k90ln7SkPPa7kLObcRCYl7SvIPCPtnKMawDhOT6wPXVxTS'
    ];


    if ($usuario !== $user['user'] || !password_verify($password.AUTH_SALT, $user['password'])) {
      Flasher::new('Las credenciales no son correctas.', 'danger');
      Redirect::back();
    }

    // Loggear al usuario
    Auth::login($user['id'], $user);
    Redirect::to('home/flash');
  }
}