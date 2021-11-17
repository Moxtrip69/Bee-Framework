<?php 

class Flasher 
{

  /**
   * El framework css definido en settings.php
   *
   * @var string
   */
  private $framework    = null;

  /**
   * Los tipos de notificación válidos
   *
   * @var array
   */
  private $valid_types = [];

  /**
   * El tipo de notificación por defecto
   * paso de ser primary a success
   *
   * @var string
   */
  private $default     = null;
  private $type        = null;
  private $msg         = null;

  function __construct()
  {
    $this->framework   = defined('CSS_FRAMEWORK') ? CSS_FRAMEWORK : 'bs5';
    $this->default     = 'success';
    $this->valid_types = ['primary','secondary','success','danger','warning','info','light','dark'];
  }

  /**
   * Método para guardar una notificación flash
   *
   * @param string array $msg
   * @param string $type
   * @return void
   */
  public static function new($msg, $type = null)
  {
    $self = new self();

    // Setear el tipo de notificación por defecto
    if($type === null) {
      $self->type = $self->default;
    }

    $self->type = in_array($type, $self->valid_types) ? $type : $self->default;

    // Guardar la notificación en un array de sesión
    if(is_array($msg)) {
      foreach ($msg as $m) {
        $_SESSION[$self->type][] = $m;
      }

      return true;
    }

    //$_SESSION['primary']['notificaciones'];
    $_SESSION[$self->type][] = $msg;

    return true;
  }

  /**
   * Crear un flash de tipo error shorthand
   *
   * @param string $msg
   * @return void
   */
  static function error(String $msg)
  {
    self::new($msg, 'danger');
    return true;
  }

  /**
   * Crear un flash de tipo info shorthand
   *
   * @param string $msg
   * @return void
   */
  static function info(String $msg)
  {
    self::new($msg, 'info');
    return true;
  }

  /**
   * Crear un flash de tipo success shorthand
   *
   * @param string $msg
   * @return void
   */
  static function success(String $msg)
  {
    self::new($msg, 'success');
    return true;
  }

  /**
   * Renderiza las notificaciones a nuestro usuario
   *
   * @return void
   */
  public static function flash()
  {
    $self        = new self();
    $placeholder = '';
    $output      = '';

    foreach ($self->valid_types as $type) {
      if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
        foreach ($_SESSION[$type] as $m) {

          switch ($self->framework) {
            case 'bl':
              $placeholder =
              '<div class="notification is-%s">
                <button class="delete delete-bulma-notification"></button>
                %s
              </div>';
              $output .= sprintf($placeholder, $type, $m);
              break;

            case 'fn':
              $placeholder =
              '<div class="callout %s" data-closable="slide-out-right">
                <h5>Notificación.</h5>
                <p>%s</p>
                <button class="close-button" aria-label="Cerrar" type="button" data-close>
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>';
              $output .= sprintf($placeholder, $self->format_type($type), $m);
              break;
            
            case 'bs5':
            case 'bs':
            default:
              $placeholder =
              '<div class="alert alert-%s alert-dismissible show fade" role="alert">
                %s 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
              </div>';
              $output .= sprintf($placeholder, $type, $m);
              break;
          }
        }

        unset($_SESSION[$type]);
      }
    }
    
    return $output;
  }

  /**
   * Muestra un mensaje de acceso denegado
   *
   * @return void
   */
  public static function deny($type = 0)
  {
    $types =
    [
      0 => 'Acceso no autorizado.',
      1 => 'Acción no autorizada.',
      2 => 'Permisos denegados.',
      3 => 'No puedes realizar esta acción.'
    ];

    self::new($types[$type], 'danger');
    return true;
  }

  /**
   * Previene errores en las clases pasadas en el parámetro tipo
   * por las diferencias entre frameworks css
   *
   * @param string $type
   * @return string
   */
  private function format_type($type)
  {
    if ($this->framework == 'fn' && $type === 'danger') {
      return 'alert';
    }

    if ($this->framework == 'fn' && $type === 'dark') {
      return 'secondary';
    }

    if ($this->framework == 'fn' && $type === 'info') {
      return 'primary';
    }

    return $type;
  }
}