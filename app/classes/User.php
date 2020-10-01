<?php 
/**
 * Contiene información de usuario
 */ 
class User extends Auth
{
  private $user      = [];
  private $is_logged = false;

  /**
   * Valida la existencia de la sesión y su validez en el sistema
   */
  public function __construct()
  {
    // Validar el estado de la sesión actual del usuario
    // Verificar la existencia de la variable de sesión
    // para prevenir errores
    $auth            = new parent();
    $this->is_logged = parent::validate();
    if ($this->is_logged === false) {
      return false;
    }
  
    // validar la existencia de la columna en la información del usuario
    if (!isset($_SESSION[$auth->__get('var')])) return false;
    if (!isset($_SESSION[$auth->__get('var')]['user'])) return false;

    $this->user = $_SESSION[$auth->__get('var')]['user'];
    return true;
  }

  /**
   * Carga el valor de una columna de la información
   * de un usuario loggeado
   *
   * @param string $column
   * @return mixed
   */
  public static function get($column)
  {
    $user = new self();
    $auth = new parent();
    if (!$user) return false;

    // Valida la existencia de la columna
    if (!isset($_SESSION[$auth->__get('var')]['user'][$column])) return false;
    return $_SESSION[$auth->__get('var')]['user'][$column];
  }

  /**
   * Método para cargar todo el perfil de un usuario guardado en sesión
   *
   * @return mixed
   */
  public static function profile()
  {
    $user = new self();
    $auth = new parent();
    if (!$user) return false;

    return $user->user;
  }
}
