<?php

class homeController extends Controller implements ControllerInterface
{
  function __construct()
  {
    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }

  function index()
  {
    $this->setTitle('Inicio');
    $this->setView('index');
    $this->render();
  }

  function auth()
  {
    $graph_version          = 'v18.0';
    $app_id                 = '332147069344931';
    $app_secret             = '5a9027fbe2f3e557d990d01ecf69368a';
    $business_id            = '133937896469155';
    $recipient_phone_number = '+525521002067';
    $redirect_uri           = URL . 'home/auth'; // La URL a la que Facebook redirigirá después de que el usuario autorice tu aplicación.
    $access_token           = isset($_SESSION["access_token"]) ? $_SESSION["access_token"] : null;
    $url                    = sprintf('https://graph.facebook.com/%s/%s/messages', $graph_version, $business_id);
    $image                  = 'https://files.cdn.thinkific.com/bundles/bundle_card_image_000/076/318/1698267779.original.jpg';

    // Construye la URL de autorización.
    $authorize_url = "https://www.facebook.com/{$graph_version}/dialog/oauth?client_id={$app_id}&redirect_uri={$redirect_uri}&scope=whatsapp_business_management";

    // Si no hay un código de autorización en la sesión, redirige al usuario para que autorice la aplicación.
    if (!isset($_GET['code'])) {
      header("Location: $authorize_url");
      exit;
    }

    // Si no existe un access token válido
    if ($access_token === null) {
      // Si el código de autorización está presente, procede a obtener el token de acceso.
      $code = $_GET['code'];
  
      // Construye la URL para solicitar el token de acceso.
      $token_url = "https://graph.facebook.com/{$graph_version}/oauth/access_token?client_id={$app_id}&redirect_uri={$redirect_uri}&client_secret={$app_secret}&code={$code}";
  
      // Realiza una solicitud para obtener el token de acceso.
      $ch = curl_init($token_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
  
      // Procesa la respuesta para obtener el token de acceso.
      $response_data = json_decode($response, true);
      logger($response_data);
  
      if (isset($response_data['error'])) {
        die($response_data['error']['message']);
      }
  
      $access_token             = $response_data['access_token'];
      $_SESSION["access_token"] = $access_token;
    }

    $data =
    [
      'messaging_product' => 'whatsapp',
      'recipient_type'    => 'individual',
      'to'                => $recipient_phone_number,
      'type'              => 'text',
      'text' =>
      [
        'preview_url' => false,
        'body'        => sprintf('Este es un nuevo mensaje usando Cloud API desde Bee framework el %s', format_date(now()))
      ] // the text object
    ];

    $data =
    [
      'messaging_product' => 'whatsapp',
      'recipient_type'    => 'individual',
      'to'                => $recipient_phone_number,
      'type'              => 'image',
      'image' =>
      [
        'link' => $image,
      ]
    ];

    $data_string = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $access_token
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    logger($result);

    curl_close($ch);

    if ($result === false) {
      die('Error al enviar el mensaje de WhatsApp.');
    }
    
    echo 'Mensaje de WhatsApp enviado correctamente.';
  }
}
