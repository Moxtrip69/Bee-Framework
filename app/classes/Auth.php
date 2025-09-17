<?php 
/**
 * Clase para crear sesiones seguras de usuarios
 * Ahora se usa BeeSessions
 */ 
class Auth
{
  /**
   * El nombre de la variable de sesión o de la clave en si
   *
   * @var string
   */
  private $var    = 'user_session';

  /**
   * Determina si el usuario está loggueado o no
   *
   * @var boolean
   */
  private $logged = false;

  /**
   * El token de acceso del usuario en curso
   *
   * @var string
   */
  private $token  = null;

  /**
   * El ID del usuario en curso
   *
   * @var mixed
   */
  private $id     = null;

  /**
   * El session_id del usuario en curso
   *
   * @var string
   */
  private $ssid   = null;

  /**
   * Toda la información registrada del usuario
   *
   * @var array
   */
  private $user   = [];

  public function __construct()
  {
    if (isset($_SESSION[$this->var])) {
      return $this;
    }

    $session =
    [
      'logged' => $this->logged,
      'token'  => $this->token,
      'id'     => $this->id,
      'ssid'   => $this->ssid,
      'user'   => $this->user
    ];

    $_SESSION[$this->var] = $session;
    return $this;
  }

  /**
   * Crea la sesión de un usuario
   * @deprecated 1.6.0
   * @param mixed $user_id
   * @param array $user_data
   * @return bool
   */
  public static function login(mixed $user_id, array $user_data = [])
  {
    $self         = new self();
    $self->logged = true;
    $session      =
    [
      'logged' => $self->logged,
      'token'  => generate_token(), // TODO: Esto ya no será necesario con el nuevo sistema de sesiones ajustar después
      'id'     => $user_id,
      'ssid'   => session_id(),
      'user'   => $user_data
    ];

    $_SESSION[$self->var] = $session;
    return true;
  }

  /**
   * Realizar la validación de la sesión del usuario en curso
   *
   * @return bool
   */
  public static function validate()
  {
    global $Bee_User;

    // Validar la sesión
    return !empty($Bee_User) ? true : false;
  }

  /**
   * Cierra la sesión del usuario en curso
   * @deprecated 1.6.0
   * @return bool
   */
  public static function logout()
  {
    $self    = new self();
    $session =
    [
      'logged' => $self->logged,
      'token'  => $self->token,
      'id'     => $self->id,
      'ssid'   => $self->ssid,
      'user'   => $self->user
    ];

    /**
     * Por seguridad
     * se destruye todo lo contenido en
     * la sesión actual del usuario
     * @since 1.1.4
     */
    $_SESSION[$self->var] = $session;
    unset($_SESSION[$self->var]);
    session_destroy();

    return true;
  }

  public function __get($var)
  {
    if (!isset($this->{$var})) return false;
    return $this->{$var};
  }
}
