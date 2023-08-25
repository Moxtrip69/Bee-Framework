<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de bee
 */
class beeController extends Controller implements ControllerInterface
{
  function __construct()
  {
    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();

    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //   Flasher::new('Debes iniciar sesión primero.', 'danger');
    //   Redirect::to('login');
    // }
  }

  function index()
  {
    /**
     * No es necesaria esta variable
     * pero así puedes registrar elementos al objeto
     * de javascript en el scope de la ruta actual
     */
    register_to_bee_obj('nuevaVariable', '123');

    // Definir og meta tags
    set_page_og_meta_tags('Bienvenido a Bee framework', null, null, null, 'website');

    $this->setTitle('Bienvenido a Bee framework');
    $this->setView('bee');
    $this->render();
  }

  /**
   * @since 1.5.0
   * 
   * Carga toda la información de bee framework en la versión actual
   *
   * @return void
   */
  function info()
  {
    echo get_bee_info();
  }

  /**
   * @since 1.5.0
   * 
   * Genera una nueva contraseña de seguridad media - alta para su uso
   *
   * @param string $password
   * @return void
   */
  function password()
  {
    $password = null;
    $errors   = 0;

    // Si el formulario fue enviado
    if (isset($_POST["password"])) {
      $password = clean($_POST["password"]);

      if (strlen($password) < 8) {
        Flasher::error('La contraseña es demasiado corta, debe contar con mínimo 8 caracteres.');
        $errors++;
      }

      if ($errors > 0) {
        $password = null;
      }
    }

    $this->setTitle('Password generado');
    $this->addToData('pw', get_new_password($password));
    $this->setView('password');
    $this->render();
  }

  /**
   * @since 1.5.0
   * 
   * Genera nuevas credenciales de acceso a la API de bee framework
   *
   * @return void
   */
  function regenerate()
  {
    try {
      if (!is_local()) {
        throw new Exception(get_bee_message(0));
      }

      if (!Csrf::validate($_GET["_t"])) {
        throw new Exception(get_bee_message('m_token'));
      }

      // Validar nombre de archivo
      $filename = 'settings.php';
      $backup   = 'settings-backup.php';
      $updated  = 0;
      $errors   = 0;

      // Validar que existe el archivo settings-backup.php por seguridad
      if (!is_file(CORE . $backup)) {
        throw new Exception(sprintf('El archivo <b>%s</b> no existe, recomendamos crear un backup de <b>%s</b> antes de proceder.', $backup, $filename));
      }

      // Validar la existencia del archivo de settings.php | solo por seguridad, en teoría esta validación ya se ha hecho anteriormente
      if (!is_file(CORE . $filename)) {
        throw new Exception(sprintf('No existe el archivo <b>%s</b>, es requerido para proceder.', $filename));
      }

      // Claves para buscar y reemplazar
      $newSettings =
      [
        'API_PUBLIC_KEY'  => "'" . generate_key() . "'",
        'API_PRIVATE_KEY' => "'" . generate_key() . "'",
        'AUTH_SALT'       => "'" . get_new_password()['hash'] . "'",
        'NONCE_SALT'      => "'" . get_new_password()['hash'] . "'"
      ];

      // Cargar contenido del archivo
      $contenido = @file_get_contents(CORE . $filename);

      // En caso de que no se lea contenido
      if (empty($contenido)) {
        throw new Exception(sprintf('Hubo un problema y no pudimos generar las credenciales de %s.', get_bee_name()));
      }

      foreach ($newSettings as $k => $v) {
        // Utilizamos una expresión regular para buscar la línea que contiene la constante
        $patron = '/define\(\s*["\']' . preg_quote($k, '/') . '["\']\s*,\s*[\'"]?.*?[\'"]?\s*\);/';

        if (preg_match($patron, $contenido, $coincidencias)) {
          // Si se encuentra la línea, reemplazamos el valor
          $lineaEncontrada  = $coincidencias[0];
          $lineaActualizada = "define('$k', $v);";
          $contenido        = str_replace($lineaEncontrada, $lineaActualizada, $contenido);
  
          // Guardamos los cambios en el archivo
          if (@file_put_contents(CORE . $filename, $contenido) === false) {
            Flasher::error(sprintf('No se pudo actualizar el valor de <b>%s</b>.', $k));
            $errors++;
            continue;
          }
          
          Flasher::success(sprintf('Valor actualizado de <b>%s</b>.', $k));
          $updated++;

        } else {
          Flasher::error(sprintf('La constante <b>%s</b> no fue encontrada en el archivo.', $k));
          $errors++;
        }
      }

      if ($updated == count($newSettings)) {
        Flasher::success(sprintf('Las claves de acceso a la API y las claves de SALT fueron generadas con éxito, las encontrarás en <b>%s</b>', $filename));
      }

      if ($errors > 0) {
        Flasher::error("Hubo <b>$errors</b> en el proceso de actualización.");
      }

      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }

  /**
   * @since 1.5.0
   * 
   * Perfil de usuario loggeado por defecto
   *
   * @return void
   */
  function perfil()
  {
    Redirect::to('admin/perfil');
  }

  /**
   * @since 1.5.5
   * 
   * Genera un usuario y lo registra en la base de datos
   *
   * @return void
   */
  function generate_user()
  {
    try {
      if (!is_local()) {
        throw new Exception(get_bee_message(0));
      }

      if (!Model::table_exists(BEE_USERS_TABLE)) {
        throw new Exception(sprintf('Es necesaria la tabla <b>%s</b> en la base de datos.', BEE_USERS_TABLE));
      }

      // Nuevo usuario
      $username = sprintf('bee%s', random_password(4, 'numeric'));
      $password = get_new_password();
      $email    = sprintf('%s@localhost.com', $username);
      $user     =
        [
          'username'   => $username,
          'password'   => $password['hash'],
          'email'      => $email,
          'created_at' => now()
        ];

      // Insertando el registro en la base de datos
      if (!$id = Model::add(BEE_USERS_TABLE, $user)) {
        throw new Exception('Hubo un problema al generar el usuario.');
      }

      Flasher::success(sprintf('Nuevo usuario generado con éxito:<br>Usuario: <b>%s</b><br>Contraseña: <b>%s</b>', $user['username'], $password['password']));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}