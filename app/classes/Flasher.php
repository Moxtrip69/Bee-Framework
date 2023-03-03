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
  public static function new(String $msg, String $type = null, String $heading = null, Bool $icon = true)
  {
    $self = new self();

    // Setear el tipo de notificación por defecto
    if($type === null) {
      $self->type = $self->default;
    }

    $self->type = in_array($type, $self->valid_types) ? $type : $self->default;

    // $_SESSION['primary']['notificaciones'];
    // Nuevos atributos añadidos
    // @since 1.5.5
    $flash = 
    [
      'type'    => $type,
      'heading' => $heading,
      'msg'     => $msg,
      'icon'    => $icon
    ];

    $_SESSION[$self->type][] = $flash;

    return true;
  }

  /**
   * Crear un flash de tipo error shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function error(String $msg, String $heading = null)
  {
    self::new($msg, 'danger', $heading);
    return true;
  }

  /**
   * Crear un flash de tipo info shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function info(String $msg, String $heading = null)
  {
    self::new($msg, 'info', $heading);
    return true;
  }

  /**
   * Crear un flash de tipo success shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function success(String $msg, String $heading = null)
  {
    self::new($msg, 'success', $heading);
    return true;
  }

  /**
   * Crear un flash de tipo warning shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function warn(String $msg, String $heading = null)
  {
    self::new($msg, 'warning', $heading);
    return true;
  }

  /**
   * Crear un flash de tipo primary shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function primary(String $msg, String $heading = null)
  {
    self::new($msg, 'primary', $heading);
    return true;
  }

  /**
   * Crear un flash de tipo dark shorthand
   *
   * @param string $msg
   * @param string $heading
   * @return void
   */
  static function dark(String $msg, String $heading = null)
  {
    self::new($msg, 'dark', $heading);
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
        foreach ($_SESSION[$type] as $f) {

          switch ($self->framework) {
            case 'bl':
              $placeholder =
              '<div class="notification is-%s">
                <button class="delete delete-bulma-notification"></button>
                %s
              </div>';
              $output .= sprintf($placeholder, $type, $f["msg"]);
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
              $output .= sprintf($placeholder, $self->format_type($type), $f["msg"]);
              break;
            
            case 'bs5':
            case 'bs':
            default:
              $placeholder = '<div class="alert alert-%s alert-dismissible show fade" role="alert">';

              if (!empty($f["heading"])) {
                $placeholder .= sprintf('<h5 class="alert-heading fw-bold">%s</h5>', $f["heading"]);
              }

              // Mostrar icono de Fontawesome 6
              if ($f["icon"] === true) {
                switch ($f["type"]) {
                  case 'primary':
                    $icon = "fas fa-bell";
                    break;

                  case "success":
                    $icon = "fas fa-check";
                    break;

                  case 'warning':
                    $icon = "fas fa-triangle-exclamation";
                    break;

                  case "danger":
                    $icon = "fas fa-xmark";
                    break;

                  case "info":
                    $icon = "fas fa-bookmark";
                    break;

                  case "dark":
                    $icon = "fas fa-bullseye";
                    break;
                  
                  default:
                    $icon = "fas fa-bell";
                    break;
                }

                $placeholder .= sprintf('<i class="%s flex-shrink-0 me-2"></i>', $icon);
              }
              
              $placeholder .= '%s
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
              </div>';

              $output .= sprintf($placeholder, $type, $f["msg"]);
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