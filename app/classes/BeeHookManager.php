<?php

class BeeHookManager
{
  /**
   * Instancia singletón de la clase BeeHookManager
   *
   * @var BeeHookManager
   */
  private static $instance = null;

  /**
   * Listado de hooks ejecutables
   *
   * @var array
   */
  private static $hooks    = [];

  /**
   * Listado de todos los hooks registrados en algún punto de ejecución
   *
   * @var array
   */
  private static $hookList = [];


  private function __construct() {
    // Constructor privado para evitar instanciación directa
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Registra un hook en la lista de hooks
   *
   * @param string $hookName
   * @param string $function
   * @return void
   */
  public static function registerHook(string $hookName, callable $function)
  {
    self::$hooks[$hookName][] = $function;
  }

  /**
   * Undocumented function
   *
   * @param string $hookName
   * @param array ...$args
   * @return void
   */
  public static function runHook(string $hookName, ...$args)
  {
    // Registrar el hook en el listado
    self::$hookList[] = $hookName;

    // Validar si existe dentro del array de hooks para ejecutar
    if (isset(self::$hooks[$hookName])) {
      foreach (self::$hooks[$hookName] as $function) {
        // if (!function_exists($function)) continue; // Permitirá generar funciones anónimas de PHP
        
        call_user_func_array($function, $args);
      }
    }
  }

  public static function runOnce(string $hookName, ...$args)
  {
    // Registrar el hook en el listado
    self::$hookList[] = $hookName;

    // Validar si existe dentro del array de hooks para ejecutar
    if (isset(self::$hooks[$hookName])) {
      foreach (array_reverse(self::$hooks[$hookName]) as $function) {
        call_user_func_array($function, $args);

        break;
      }
    }
  }

  public static function getHookData(string $hookName, ...$args)
  {
    $hookData = [];

    if (isset(self::$hooks[$hookName])) {
      foreach (self::$hooks[$hookName] as $function) {
        $hookData[] = call_user_func_array($function, $args);
      }
    }

    return $hookData;
  }

  /**
   * Regresa el listado de hooks ejecutables
   *
   * @return array
   */
  public static function getHooks()
  {
    return self::$hooks;
  }

  /**
   * Regresa el listaod de todos los hooks registrados
   *
   * @return void
   */
  public static function getHookList()
  {
    return self::$hookList;
  }
}
