<?php

class Autoloader
{
  /**
   * Método encargado de ejecutar el autocargador de forma estática
   *
   * @return void
   */
  public static function init()
  {
    spl_autoload_register([__CLASS__, 'autoload']);
  }

  /**
   * Se ejecuta cada que se requiere cargar una clase
   *
   * @param string $class_name
   * @return void
   */
  private static function autoload($class_name)
  {
    $filename = sprintf('%s.php', $class_name);
    $paths    =
    [
      CLASSES,
      CONTROLLERS,
      MODELS,
      APP . 'services' . DS,
      APP . 'utils' . DS
    ];

    foreach ($paths as $path) {
      if (is_file($path . $filename)) {
        require_once $path . $filename;
      }
    }
  }
}
