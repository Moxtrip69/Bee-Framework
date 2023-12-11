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
    if (!is_local()) {
      die(get_bee_message(0));
    }
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
        throw new Exception(sprintf('Hubo un problema y no pudimos generar las credenciales de %s, el contenido del archivo está vacío.', get_bee_name()));
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

  /**
   * @since 1.5.5
   * 
   * Prueba general de uso de Twig
   *
   * @return void
   */
  function twig()
  {
    // Renderizar la plantilla
    $this->setTitle('Mi página con Twig');
    $this->addToData('name', 'Usuario bien Cool');
    $this->setView('test');
    $this->setEngine('twig');
    $this->render();
  }

  /**
   * Ejemplo de uso con vuejs3
   *
   * @return void
   */
  function vuejs()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS . 'vueApp.min.js'], 'Bee framework vuejs 3');

    $this->setTitle('Ejemplo de administrador de tareas');
    $this->setView('vuejs');
    $this->render();
  }

  /**
   * Ejemplo de componente individual con Vuejs3
   *
   * @return void
   */
  function test_component()
  {
    /**
     * Registro de scripts para solo está ruta
     */
    register_scripts([JS . 'vueApp.min.js'], 'Bee framework vuejs 3');

    $this->setTitle('Componente de prueba');
    $this->setView('testVuejs');
    $this->render();
  }

  /**
   * Funció para actualizar el core del framework
   * @since 1.5.81
   *
   * @return void
   */
  function upgrade_core()
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET["_t"])) {
        throw new Exception('Acción no autorizada.');
      }

      $coreVersion = function_exists('get_core_version') ? get_core_version() : '1.0.0';
      $beeVersion  = get_bee_version();
      $gitUser     = 'Moxtrip69';
      $repoName    = 'Bee-Framework';
      $repoUrl     = sprintf('https://github.com/%s/%s/archive/refs/heads/%s.zip', $gitUser, $repoName, $beeVersion);
      $tmp         = 'BeeDownloaded';
      $file        = sprintf('%s.zip', $tmp);
      $start       = time();
      $end         = 0;

      logger('|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||');
      logger('---------- Inicia la actualización del core de Bee framework ----------');

      // Descargar el archivo Zip del repositorio oficial
      if (file_put_contents(UPLOADS . $file, file_get_contents($repoUrl)) === false) {
        throw new Exception('Hubo un problema al descargar la actualización desde Github.');
      }

      // Verificar si la descarga fue exitosa
      if (!is_file(UPLOADS . $file)) {
        throw new Exception('Error al descargar el archivo zip.');
      }

      logger(sprintf('Repositorio descargado con éxito: %s', UPLOADS . $file));

      // Abrir el archivo Zip descargado
      $zip = new ZipArchive;
      $res = $zip->open(UPLOADS . $file);

      if ($res !== true) {
        throw new Exception('Error al extraer el contenido del archivo zip.');
      }

      logger('Extrayendo repositorio temporal...');

      $zip->extractTo(UPLOADS . $tmp);
      $zip->close();

      // Eliminar el Zip después de la extracción completada
      if (!unlink(UPLOADS . $file)) {
        throw new Exception(sprintf('Error al borrar el archivo descargado en: %s.', UPLOADS . $file));
      }

      logger('Eliminando repositorio temporal...');

      // Path de origen de la actualización
      $origen          = UPLOADS . $tmp . DS . sprintf('%s-%s', $repoName, $beeVersion) . DS;

      // Sustituir archivos necesarios
      $filesToUpdate   = [];

      // Htaccess general
      $filesToUpdate[] = '.htaccess';

      // Composer json
      $filesToUpdate[] = 'app' . DS . 'composer.json';

      // Versión del core remota
      $filesToUpdate[] = 'app' . DS . 'core' . DS . 'bee_core_version.php';
      $filesToUpdate[] = 'app' . DS . 'core' . DS . 'update.txt';
      // $filesToUpdate[] = 'app' . DS . 'core' . DS . 'settings.php'; // Próximamente

      // Config y settings
      // $filesToUpdate[] = 'app' . DS . 'config' . DS . 'bee_config.php';

      // Clases
      $filesToUpdate = array_merge($filesToUpdate, glob('app' . DS . 'classes' . DS . '*.php'));

      // Funciones del core
      $filesToUpdate[] = 'app' . DS . 'functions' . DS . 'bee_core_functions.php';

      // Controladores
      $elements = ['bee','creator','admin'];
      foreach ($elements as $el) {
        $filesToUpdate[] = 'app' . DS . 'controllers' . DS . $el . 'Controller.php';
      }

      // Vistas
      $filesToUpdate = array_merge($filesToUpdate, glob('templates' . DS . 'views' . DS . '*' . DS . '*View.php'));

      // Modelos
      $filesToUpdate = array_merge($filesToUpdate, glob('app' . DS . 'models' . DS . '*Model.php'));

      // Testing
      // $filesToUpdate = [ 'app' . DS . 'core' . DS . 'update.txt' ];

      // Verificar la versión remota del core
      $newCoreVersion = require $origen . 'app' . DS . 'core' . DS . 'bee_core_version.php';
      logger(sprintf('Versión actual: %s | Versión remota: %s', $coreVersion, $newCoreVersion));

      if (version_compare($coreVersion, $newCoreVersion, '=')) {
        // Borrar la carpeta duplicada
        remove_dir(UPLOADS . $tmp);
        throw new Exception('La versión del core remota es igual a la versión actual de tu instancia.');
      }

      if (version_compare($coreVersion, $newCoreVersion, '>')) {
        // Borrar la carpeta duplicada
        remove_dir(UPLOADS . $tmp);
        throw new Exception('La versión del core remota es menor a la versión actual de tu instancia.');
      }

      logger('Comenzando actualización de archivos...');

      // Iteración y sustitución de archivos
      $copied = 0;
      $errors = 0;

      foreach ($filesToUpdate as $f) {
        if (!is_file($origen . $f)) {
          $errors++;
          logger(sprintf('Hubo un error con el archivo: %s', $origen . $f));
        }
        
        copy($origen . $f, ROOT . $f);
        $copied++;
        logger(sprintf('Archivo actualizado: %s', ROOT . $f));
      }

      // Borrar la carpeta duplicada
      remove_dir(UPLOADS . $tmp);
      
      // Termina el proceso
      $end = time();

      logger(sprintf('Se ha borrado la carpeta temporal: %s', UPLOADS . $tmp));
      
      $msg = sprintf('Hemos actualizado el core de tu instancia de Bee framework %s con éxito.', $beeVersion);
      
      Flasher::success($msg, 'Actualización completada');
      logger($msg);
      logger(sprintf('Se actualizaron %s archivos con éxito.', $copied));
      logger(sprintf('Hubo errores en %s archivos.', $errors));
      logger(sprintf('Tiempo transcurrido: %ss.', $end - $start));
      logger(sprintf('Versión actual: %s | Versión actualizada: %s', $coreVersion, $newCoreVersion));

      if ($errors > 0) {
        Flasher::error(sprintf('Hubo <b>%s</b> errores en la actualización del core.', $errors));
      }

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
    }

    logger('---------- Termina la actualización del core de Bee framework ----------');
    logger('|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||');
    logger('');
    
    Redirect::back();
  }
}