<?php 

class Flasher 
{

  private $valid_types = ['primary','secondary','success','danger','warning','info','light','dark'];
  private $default = 'primary';
  private $type;
  private $msg;

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
   * Renderiza las notificaciones a nuestro usuario
   *
   * @return void
   */
  public static function flash()
  {
    $self = new self();
    $output = '';

    foreach ($self->valid_types as $type) {
      if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
        foreach ($_SESSION[$type] as $m) {
          $output .= '<div class="alert alert-'.$type.' alert-dismissible show fade" role="alert">';
          $output .= $m;
          $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
          $output .= '</div>';
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
}