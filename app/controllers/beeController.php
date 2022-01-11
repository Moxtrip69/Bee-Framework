<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de bee
 */
class beeController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    /**
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
    */
  }

  function index()
  {
    /**
     * No es necesaria esta variable
     * pero así puedes registrar elementos al objeto
     * de javascript en el scope de la ruta actual
     */
    register_to_bee_obj('nuevaVariable', '123');

    $data =
    [
      'title' => 'Bienvenido'
    ];

    View::render('bee', $data);
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
  function password($password = null)
  {
    $data =
    [
      'title' => 'Password Generado',
      'pw'    => get_new_password($password)
    ];

    echo get_module('bee/password', $data);
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

      // Validar que existe el archivo settings-backup.php por seguridad
      if (!is_file(CORE.$backup)) {
        throw new Exception(sprintf('El archivo %s no existe, recomendamos crear un backup de %s antes de proceder.', $backup, $filename));
      }
  
      // Validar la existencia del archivo de settings.php | solo por seguridad, en teoría esta validación ya se ha hecho anteriormente
      if (!is_file(CORE.$filename)) {
        throw new Exception(sprintf('No existe el archivo %s, es requerido para proceder.', $filename));
      }

      // Keys a insertar en el archivo
      $key1 = generate_key(); // public key
      $key2 = generate_key(); // private key
      
      // Cargar contenido del archivo
      $php = @file_get_contents(CORE.$filename);

      // En caso de que no se lea contenido

      if (empty($php)) {
        throw new Exception(sprintf('Hubo un problema y no pudimos generatas las API keys para esta instancia de %s.', get_bee_name()));
      }

      $php = str_replace('[[REPLACE_PUBLIC_KEY]]' , $key1, $php, $ok1);
      $php = str_replace('[[REPLACE_PRIVATE_KEY]]', $key2, $php, $ok2);

      // Validar que se hayan reemplazado con éxito ambas
      if ($ok1 == 0 && $ok2 == 0) {
        throw new Exception(sprintf('No pudimos reemplazar las API keys en tu archivo <b>%s</b>, es probable que debas sustituir el contenido de <b>%s</b> con el de <b>%s</b>.',
          $filename,
          $filename,
          $backup
        ));
      }

      // Guardar los cambios en el archivo settings.php
      if (file_put_contents(CORE.$filename, $php) === false)  {
        throw new Exception(sprintf('Ocurrió un problema al actualizar el archivo.', $filename));
      }

      Flasher::success(sprintf('API keys generadas con éxito, las encontrarás en <b>%s</b>', $filename));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }

  /**
   * @since 1.1.3
   * 
   * Genera un PDF de forma sencilla y dinámica
   *
   * @return void
   */
  function pdf()
  {
    try {
      $content = '<!DOCTYPE html>
      <html>
      <head>
      <style>
      code {
        font-family: Consolas,"courier new";
        color: crimson;
        background-color: #f1f1f1;
        padding: 2px;
        font-size: 80%%;
        border-radius: 5px;
      }
      </style>
      </head>
      <body>
  
      <img src="%s" alt="%s" style="width: 100px;"><br>
  
      <h1>Bienvenido de nuevo a %s</h1>
      <p>Versión <b>%s</b></p>
      
      <code>
      // Método 1
      $content = "Contenido del documento PDF, puedes usar cualquier tipo de HTML e incluso la mayoría de estilos CSS3";
      $pdf     = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true
  
      // Método 2
      $pdf = new BeePdf();
      $pdf->create("bee_pdfs", $content);
      </code>
  
      </body>
      </html>';
      $content = sprintf($content, get_bee_logo(), get_bee_name(), get_bee_name(), get_bee_version());
  
      // Método 1
      $pdf = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true
  
      // Método 2
      //$pdf = new BeePdf();
      //$pdf->create('bee_pdfs', $content);

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('home');
    }

  }

  /**
   * Prueba para enviar correos electrónicos regulares
   *
   * @return void
   */
  function email()
  {
    try {
      $email   = 'jslocal2@localhost.com';
      $subject = 'El asunto del correo';
      $body    = 'El cuerpo del mensaje, puede ser html o texto plano.';
      $alt     = 'El texto corto del correo, preview del contenido.';
      send_email(get_siteemail(), $email, $subject, $body, $alt);
      echo sprintf('Correo electrónico enviado con éxito a %s', $email);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  /**
   * @since 1.5.0
   * 
   * Prueba de envío de correos electrónicos usando SMTP
   *
   * @return void
   */
  function smtp()
  {
    try {
      send_email('tuemail@hotmail.com', 'tuemail@hotmail.com', 'Probando smtp', '¡Hola mundo!', 'Correo de prueba.');
      echo 'Mensaje enviado con éxito.';
    } catch (Exception $e) {
      echo $e->getMessage();
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
    parent::auth();

    $data =
    [
      'title' => 'Perfil de usuario',
      'user'  => User::profile()
    ];

    View::render('perfil', $data);
  }
}