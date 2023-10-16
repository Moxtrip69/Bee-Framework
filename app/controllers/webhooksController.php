<?php
/**
 * Plantilla general de controladores
 * @version 2.0.0
 *
 * Controlador de webhooks
 */
class webhooksController extends Controller implements ControllerInterface
{
  function __construct()
  {
    // Prevenir el ingreso si nos encontramos en producción y esta ruta es sólo para desarrollo o pruebas
    // if (!is_local()) {
    //   Redirect::to(DEFAULT_CONTROLLER);
    // }
    
    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //  Flasher::new('Debes iniciar sesión primero.', 'danger');
    //  Redirect::to('login');
    // }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index()
  {
    http_response_code(404);
  }

  function whatsapp()
  {
    $hash        = '$2y$10$SD0ZBeTAdT3FY9Gqw.h4ZeSc2x2NP3gJMwx7gHcgLI9qXUIne81lK';
    $verifyToken = isset($_GET["hub_verify_token"]) ? $_GET['hub_verify_token'] : null;
    $challenge   = isset($_GET["hub_challenge"]) ? $_GET['hub_challenge'] : null;

    $body['GET']  = $_GET;
    $body['POST'] = isset($_POST) ? $_POST : [];
    $body['BODY'] = @file_get_contents('php://input');
    send_email('noreply@joystick.com.mx', 'hellow@joystick.com.mx', 'Evento recibido', print_r($body, true));

    if ($verifyToken !== null && $hash !== $verifyToken) {
      http_response_code(403);
      die;
    }

    echo $challenge;
  }
}