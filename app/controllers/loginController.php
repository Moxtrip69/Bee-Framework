<?php 

class loginController extends Controller implements ControllerInterface 
{
  function __construct()
  {
    if (Auth::validate()) {
      Flasher::new('Ya hay una sesión abierta.');
      Redirect::to('admin/perfil');
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }

  function index()
  {
    $this->setTitle('Ingresa a tu cuenta');
    $this->setView('login');
    $this->render();
  }

  function post_login()
  {
    try {
      if (!check_posted_data(['usuario', 'csrf', 'password'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_bee_message(0));
      }
  
      // Data pasada del formulario
      $usuario  = sanitize_string($_POST['usuario'], 120);
      $password = (string) $_POST['password'];
  
      // Verificar información del usuario
      if (!$user = Model::list(BEE_USERS_TABLE, ['username' => $usuario], 1)) {
        throw new Exception('Las credenciales no son correctas.');
      }

      // Verifica el password del usuario con base al ingresado y el de la db
      if (!password_verify($password . AUTH_SALT, $user['password'])) {
        throw new Exception('Las credenciales no son correctas.');
      }

      // Sesiones totalmente persistentes con base a cookies y multidispositivos
      BeeSession::new_session($user);

      // Recargar información de usuario
      // @deprecated 1.6.0 ya no es necesario
      // $user = Model::list(BEE_USERS_TABLE, ['id' => $user['id']], 1);

      // Registrar la información en sesión
      // @deprecated 1.6.0 ya no es necesario
      // Auth::login($user['id'], $user);

      // Redirección a la página inicial después de log in
      Redirect::to('admin');

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}
