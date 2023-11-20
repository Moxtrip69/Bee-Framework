<?php 

//////////////////////////////////////////////////
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as EmailException;
//////////////////////////////////////////////////

class BeeMailer
{
  /**
   * Instancia de PHPMailer
   *
   * @var PHPMailer
   */
  private $mailer      = null;

  /**
   * Activar modo verboso
   *
   * @var bool
   */
  private $debug       = PHPMAILER_DEBUG;

  /**
   * Usar servidor SMTP
   *
   * @var bool
   */
  private $smtp        = PHPMAILER_SMTP;

  /**
   * Tipo de seguridad ssl o tls
   *
   * @var string
   */
  private $security    = PHPMAILER_SECURITY;

  /**
   * Nombre de la plantilla a usar para los correos
   *
   * @var string
   */
  private $template    = PHPMAILER_TEMPLATE;

  /**
   * Si será o no usada la plantilla
   *
   * @var boolean
   */
  private $useTemplate = false;

  /**
   * Servidor o host remoto
   *
   * @var string
   */
  private $host        = PHPMAILER_HOST;

  /**
   * Dirección de correo electrónico remoto
   *
   * @var string
   */
  private $username    = PHPMAILER_USERNAME;

  /**
   * Contraseña o token de la dirección de correo
   *
   * @var string
   */
  private $password    = PHPMAILER_PASSWORD;

  /**
   * Puerto del servidor remoto
   *
   * @var int
   */
  private $port        = PHPMAILER_PORT;

  /**
   * Charset del contenido
   *
   * @var string
   */
  private $charset     = 'UTF-8';

  /**
   * Dirección que será mostrada como remitente
   *
   * @var string
   */
  private $from        = null;

  /**
   * Nombre para mostrar del remitente
   *
   * @var string
   */
  private $fromName    = null;

  /**
   * Direcciones de correo de destinatarios
   *
   * @var array
   */
  private $to          = [];

  /**
   * Asunto del correo
   *
   * @var string
   */
  private $subject     = 'Nuevo correo electrónico';

  /**
   * Texto corto o alt del correo
   *
   * @var string
   */
  private $alt         = 'Un nuevo correo electrónico.';

  /**
   * Contenido del correo electrónico
   *
   * @var string
   */
  private $body        = 'Cuerpo del correo electrónico';

  /**
   * Adjuntos del correo electrónico
   *
   * @var array
   */
  private $attachments = [];

  function __construct()
  {
    $this->mailer   = new PHPMailer(PHPMAILER_EXCEPTIONS);
    $this->from     = get_siteemail();
    $this->fromName = get_sitename();
  }

  function enableSmtp()
  {
    $this->smtp = true;
  }

  function disableSmtp()
  {
    $this->smtp = false;  
  }

  function enableDebug()
  {
    $this->debug = true;
  }

  function disableDebug()
  {
    $this->debug = false;
  }

  function setAuthentication(string $username, string $password, string $host, string $port, string $security)
  {
    $this->username = $username;
    $this->password = $password;
    $this->host     = $host;
    $this->port     = $port;
    $this->security = $security;
  }

  function setCharset(string $charset)
  {
    $this->charset = $charset;
  }

  function setFrom(string $email)
  {
    $this->from     = $email;
  }
  
  function setFromName(string $name)
  {
    $this->fromName = $name;  
  }

  function sendTo(string $email)
  {
    $this->to[] = $email;
  }

  function addDestinatary(string $email)
  {
    $this->to[] = $email;
  }

  function addDestinataries(array $emails)
  {
    $this->to = $emails;
  }

  function setSubject(string $subject)
  {
    $this->subject = $subject;
  }

  function setAlt(string $alt)
  {
    $this->alt = $alt;
  }

  function setBody(string $body)
  {
    $this->body = $body;
  }

  function addAttachment(string $attachment)
  {
    $this->attachments[] = $attachment;
  }

  function useTemplate(bool $use)
  {
    $this->useTemplate = $use;
  }

  /**
   * Envía el correo electrónico completamente con todas las configuraciones actuales
   *
   * @return void
   */
  function send()
  {
    try {
      // Conexión SMTP y settings del servidor
      if ($this->smtp === true) {
        $this->mailer->isSMTP();

        // Modo verboso de conexión
        if ($this->debug === true) {
          $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        // Credenciales de conexión SMTP
        $this->mailer->SMTPAuth   = $this->smtp;
        $this->mailer->Host       = $this->host;
        $this->mailer->Username   = $this->username;
        $this->mailer->Password   = $this->password;
        $this->mailer->SMTPSecure = $this->security === 'ssl' ?
          PHPMailer::ENCRYPTION_SMTPS :
          PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $this->port;
      }

      // Charset del contenido
      $this->mailer->CharSet = $this->charset;

      // Remitente
      $this->mailer->setFrom($this->from, $this->fromName);

      // Destinatarios
      foreach ($this->to as $address) {
        $this->mailer->addAddress($address);
      }

      // Añadir adjunto al correo
      if (!empty($this->attachments)) {
        foreach ($this->attachments as $attachment) {
          if (!is_file($attachment)) {
            continue;
          }
  
          $this->mailer->addAttachment($attachment); // se anexa el adjunto al correo
        }
      }

      // Asunto y alt general del correo
      $this->mailer->isHTML(true);
      $this->mailer->Subject = $this->subject;
      $this->mailer->AltBody = $this->alt;

      // Contenido del correo
      if ($this->useTemplate === true) {
        $data    =
        [
          'alt'     => $this->alt,
          'subject' => $this->subject,
          'body'    => $this->body
        ];
        $this->mailer->Body = get_module($this->template, $data);  
      } else {
        $this->mailer->Body = $this->body;
      }

      // Enviar el correo electrónico
      $this->mailer->send();
      $this->mailer->clearAllRecipients();
      return true;

    } catch (EmailException $e) {
      throw new Exception($e->getMessage());
    }
  }
}
