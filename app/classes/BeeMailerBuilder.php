<?php 

class BeeMailerBuilder
{
  private $data = [];
  private $subject;
  private $alt;
  private $image;
  private $body;
  private $button;

  function __construct(?array $data = null)
  {
    if (!empty($data)) $this->data = $data;
  }

  // ASUNTO Y TEXTO ALTERNATIVO
  function setSubject(string $subject, string $alt)
  {
    $this->subject = $subject;  
    $this->alt     = $alt;
  }

  // DEBEMOS PODER ESTABLECER LA IMAGEN DEL CORREO
  function setImage(string $src, ?string $alt, ?string $url = null)
  {
    if (!$url) {
      $this->image = sprintf('<img src="%s" alt="%s" class="img-fluid">', $src, $alt);
    } else {
      $this->image = sprintf('<a href="%1$s"><img src="%2$s" alt="%3$s" class="img-fluid"></a>', $url, $src, $alt);
    }

    return $this;
  }

  // ESTABLECER EL BOTÓN
  function setButton(string $url, string $text)
  {
    $this->button = sprintf('<div style="margin-top: 20px; margin-bottom: 20px;"><a class="btn btn-primary" href="%s">%s</a></div>', $url, $text);

    return $this;
  }

  // EL CUERPO DEL CORREO
  function setBody(string $body)
  {
    $this->body = $body;
  }

  private function buildBody()
  {
    $body = '';

    // Agregar la imagen superior del correo
    if ($this->image) {
      $body .= $this->image . '<br>';
    }

    // Cuerpo principal del correo electrónico
    $body .= $this->body;

    // Agregar el botón si existe
    if ($this->button) {
      $body .= '<br><br>' . $this->button;
    }

    return $body;
  }

  private function send()
  {
    try {
      $usuario      = PHPMAILER_USERNAME;
      $password     = PHPMAILER_PASSWORD;
      $servidor     = PHPMAILER_HOST;
      $puerto       = PHPMAILER_PORT;
      $seguridad    = PHPMAILER_SECURITY;
      $template     = PHPMAILER_TEMPLATE;

      // Variaciones directas del diseño
      $from_admin   = $this->data['from_admin'] ?? false;
      $poweredby    = $from_admin;

      // Destinatario
      $destinatario = is_local() ? $usuario : $this->data['email'];

      // Inicializar
      $mail = new BeeMailer();
      $mail->enableSmtp();
      $mail->disableDebug();
      $mail->setAuthentication($usuario, $password, $servidor, $puerto, $seguridad);
      $mail->setFrom($usuario);
      $mail->setFromName(get_sitename());
      $mail->setSubject($this->subject);
      $mail->setAlt($this->alt);
      $mail->setBody(get_module($template, [
        'subject'    => $this->subject,
        'alt'        => $this->alt, 
        'body'       => $this->buildBody(), 
        'from_admin' => $from_admin,
        'poweredby'  => $from_admin,
      ]));
      $mail->sendTo($destinatario);
      $mail->send();
      $mail->disconnect();

      return true;

    } catch (Exception $e) {
      logger('Hubo un error al enviar un correo: ' . $e->getMessage());
      return false;
    }
  }

  ////////////////////////////////////
  ////////////////////////////////////
  // MÉTODOS RÁPIDOS PARA NOTIFICACIONES
  ////////////////////////////////////
  ////////////////////////////////////

  static function example($data)
  {
    $url  = build_url(URL . 'login/activate', ['hash' => $data['hash']], false, false);
    $mail = new self($data);
    $mail->setSubject(
      sprintf('📩 Confirma tu correo eletrónico por favor %s', $data['nombre_completo']),
      sprintf('Debes confirmar tu correo electrónico para poder ingresar a %s.', get_sitename())
    );
    $mail->setImage(get_image('email-usuario-confirmacion.png'), 'Confirma tu cuenta');
    $mail->setButton($url, 'Confirmar cuenta');
    $mail->setBody(
      sprintf(
        '¡Hola %s!<br>Para ingresar a tu cuenta en <b>%s</b>, primero debes confirmar tu dirección de correo electrónico dando clic en el siguiente enlace seguro o en el botón:<br>',
        $data['nombres'],
        get_sitename()
      )
    );
    $mail->send();
  }
}
