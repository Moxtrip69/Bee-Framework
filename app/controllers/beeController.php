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
    echo get_bee_info();
  }

  function password($password = null)
  {
    $data =
    [
      'title' => 'Password Generado',
      'pw'    => get_new_password($password)
    ];

    echo get_module('bee/password', $data);
  }

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
}