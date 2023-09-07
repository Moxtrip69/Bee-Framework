<?php 

/**
 * @since 1.1.4
 * 
 * Clase encargada de realizar el proceso de peticiones recibidas por las rutas
 * de ajaxController y apiController y sus variantes para controlar
 * peticiones http y consumir recursos o servicios de la REST Api
 * de bee framework.
 * 
 * Esta clase implementa las funcionalidades anteriores de bee ajaxController
 * en su versión 1.1.3
 * 
 * @version 1.0.2
 * 
 */
class BeeHttp
{
  /**
   * Determina el tipo de llamada
   * si es api se refiere a que se está solicitando apiController
   * o si es ajax se refiere a que se está solicitando ajaxController
   *
   * @var string
   */
  private $call   = null;

  /**
   * El verbo de la petición realizada al servidor
   *
   * @var string
   */
  private $r_type = null;

  /**
   * Token csrf de la sesión del usuario que solicita la petición
   * solo disponible para ser usado en peticiones ajax POST
   * API requiere una autenticación diferente
   *
   * @var string
   */
  private $csrf   = null;

  /**
   * Todos los parámetros recibidos de la petición
   *
   * @var array
   */
  private $data   = null;

  /**
   * @since 1.1.4
   *
   * @var mixed
   */
  private $body   = null;

  /**
   * Parámetros parseados en caso de ser petición put | delete | headers | options
   *
   * @var mixed
   */
  private $parsed = null;

  /**
   * Array de archivos binarios pasados
   * en petición POST al servidor
   * 
   * @since 1.1.4
   *
   * @var array
   */
  private $files  = [];

  /**
   * Posibles verbos o acciones disponibles para nuestra petición
   * 
   * Actualizados
   * @since 1.1.4
   *
   * @var array
   */
  private $accepted_verbs = 
  [
    'GET', 
    'POST', 
    'PUT', 
    'PATCH',
    'DELETE', 
    'COPY',
    'HEAD',
    'OPTIONS', 
    'LINK',
    'UNLINK',
    'PURGE',
    'LOCK',
    'UNLOCK',
    'PROPFIND',
    'VIEW'
  ];

  /**
   * Cabeceras de la petición entrante
   * 
   * @since 1.1.4
   *
   * @var array
   */
  private $headers          = [];

  /**
   * API Keys recibidas para consumir ciertos recursos
   * solo en caso de ser necesarias
   * 
   * @since 1.1.4
   *
   * @var string
   */
  private $public_key       = null;
  private $private_key      = null;

  /**
   * Determina si la petición está siendo realizada en un servidor
   * apache, así podemos acceder a las cabeceras y su contenido
   * de forma sencilla usando PHP apache_request_headers
   * 
   * @since 1.1.4
   *
   * @var boolean
   */
  private $apache_request   = false;

  /**
   * Determina si es requerido validar que exista una key de
   * autorización en las cabeceras de la petición
   * 
   * Solo es utilizado si es una petición http de consumo de la API
   * para peticiones ajax no es requerido ni válido
   * 
   * @since 1.1.4
   *
   * @var boolean
   */
  private $authenticate     = false;

  /**
   * Procolo de la URL definido en settings.php
   * puede ser http o https según sea el caso
   * 
   * Esto genera una variación en las cabeceras enviadas, es necesario para
   * leer de forma correcta los parámetros de autorización
   * Auth_private_key o auth_private_key
   * 
   * @since 1.5.5
   *
   * @var string
   */
  private $protocol         = null;

  /**
   * El dominio de origen de la petición, para autorizar cuando la API se consume
   * o se trata de acceder desde otro diferente
   * 
   * @since 1.5.5
   *
   * @var string
   */
  private $origin           = '';

  /**
   * Dominios autorizados para acceder a los recursos
   * 
   * @since 1.5.5
   *
   * @var array
   */
  private $domains          = [];

  function __construct(array $options = [])
  {
    // Autenticación de acceso con Bearer Token en headers
    $this->authenticate = isset($options['authenticate']) ? $options['authenticate'] : bee_api_authentication();

    // Dominios para CORS
    $this->domains      = isset($options['domains']) ? $options['domains'] : [];

    // El verbo HTTP utilizado en la petición
    $this->r_type       = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

    // Protocolo http de la petición
    $this->protocol     = PROTOCOL; // definido en settings.php
  }

  /**
   * Verifica que la petición tenga un verbo aceptado en la lista de verbos aceptados
   *
   * @return void
   */
  private function validateRequestVerb()
  {
    if(!in_array(strtoupper($this->r_type), $this->accepted_verbs)) {
      throw new BeeHttpException(
        'Verbo HTTP no aceptado.', 
        403
      ); // 403
    }
  }

  function process()
  {
    /**
     * @since 1.1.4
     */
    // Validar que se pase un verbo válido y aceptado
    $this->validateRequestVerb();

    // Se encarga de validar la petición OPTIONS de preflight para CORS
    $this->handlePreflightRequest();

    // Almacenando y determinando las cabeceras recibidas
    $this->get_headers();

    // Establece las API keys en caso de estar presentes en la petición
    $this->set_api_keys();
    
    // Parsing del cuerpo de la petición
    $this->parse_body();

    // Validar token CSRF
    $this->check_csrf();
    
    // Validar de la petición post / put / delete el token csrf
    $this->validate_csrf();
  }

  function registerDomain(string $domain)
  {
    $this->domains[] = $domain;
  }

  private function handlePreflightRequest()
  {
    // Establecer las cabeceras CORS para la Preflight Request
    // Obtener el valor del origen de la solicitud desde el encabezado
    $this->origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;

    // Verificar si el origen está en la lista de dominios permitidos
    if (in_array($this->origin, $this->domains)) {
      // Establecer el valor del origen permitido en la cabecera Access-Control-Allow-Origin
      header('Access-Control-Allow-Origin: ' . $this->origin);
    } else if(in_array('*', $this->domains)) {
      // Permitir cualquier origen (dominio) para acceder a tu API
      header('Access-Control-Allow-Origin: *');
    } else {
      // Permitir sólo el dominio base por defecto
      header('Access-Control-Allow-Origin: ' . URL);
    }

    // Especificar los métodos HTTP permitidos para acceder a tu API
    header(sprintf('Access-Control-Allow-Methods: %s', rtrim(implode(',', $this->accepted_verbs), ',')));

    if ($this->authenticate === true) {
      // Permitir ciertos encabezados personalizados en las solicitudes
      header('Access-Control-Allow-Headers: Content-Type, Authorization');

      // Indicar si las credenciales (cookies, autenticación HTTP) se pueden incluir en la solicitud desde el cliente
      header('Access-Control-Allow-Credentials: true');

    } else {
      header('Access-Control-Allow-Headers: Content-Type');
    }

    // Verificar si la solicitud es una Preflight Request (OPTIONS)
    if ($this->r_type === 'OPTIONS') {
      http_response_code(200);
      exit(); // Terminar el script después de enviar las cabeceras de respuesta
    }
  }

  /**
   * Carga todas las cabeceras de la petición
   * se almacenan en nuestra propiedad $headers
   * 
   * @since 1.1.4
   *
   * @return void
   */
  private function get_headers()
  {
    $this->apache_request = false;

    if (function_exists('apache_request_headers')) {
      $this->headers        = apache_request_headers();
      $this->apache_request = true;
      $this->headers        = isset($_SERVER) ? array_merge($this->headers, $_SERVER) : $this->headers;
    } else {
      $this->headers        = isset($_SERVER) ? $_SERVER : $this->headers;
    }
  }
  
  /**
   * Buscamos dentro de las cabeceras de la petición
   * si se han enviado alguna de las API keys de autenticación
   * para consumir los recursos de la API.
   * 
   * Se ha actualizado el nombre de nuestra cabecera de autorización para
   * mejorar la seguridad e ir a la par con estándares actuales
   * 
   * @since 1.1.4
   *
   * @return void
   */
  private function set_api_keys()
  {
    // Verificar si la cabecera 'Authorization' está presente en la solicitud
    if (isset($this->headers['HTTP_AUTHORIZATION'])) {
      // Obtener el valor del encabezado 'Authorization'
      $authorizationHeader = $this->headers['HTTP_AUTHORIZATION'];

      // Comprobar si el encabezado contiene el prefijo "Bearer "
      if (preg_match('/^Bearer\s+(.*)$/', $authorizationHeader, $matches)) {
        $this->private_key = $matches[1];
      }
    }
  }

  /**
   * Compara la api key privada de esta instancia de bee framework
   * con la enviada en las cabeceras de la petición http para autorizar el acceso
   *
   * @return true si es correcto | excepción si no lo es
   */
  public function authenticate_request()
  {
    if ($this->authenticate === false) return true; // no es necesaria la autenticación
    
    $api_key = get_bee_api_private_key();

    if (strcmp($api_key, $this->private_key) !== 0) {
      throw new BeeHttpException(get_bee_message(0), 403);
    }

    return true;
  }

  function setAuthentication(bool $authenticate)
  {
    $this->authenticate = $authenticate;
  }

  /**
   * Verifica y establece el valor del token CSRF enviado por el usuario
   * en la petición en
   *
   * @return void
   */
  private function check_csrf()
  {
    $this->csrf = isset($this->data['csrf']) ? $this->data['csrf'] : null;
  }

  /**
   * Establece el tipo de llamada o petición realizada
   *
   * @param string $callType
   * @return void
   */
  function setCallType(string $callType)
  {
    $this->call = $callType;
  }

  /**
   * Valida que el token CSRF enviado en la petición
   * sea correcto y válido para el usuario en curso
   *
   * @return void
   */
  private function validate_csrf()
  {
    if ($this->call === 'ajax' && in_array(strtolower($this->r_type), ['post', 'put', 'delete']) && !Csrf::validate($this->csrf)) {
      throw new BeeHttpException('Autorización no válida.', 401); // 401
    }
  }

  /**
   * Parsea el contenido del cuerpo de la petición con
   * base al verbo o tipo de petición realizada
   * @since 1.1.4
   *
   * @return void
   */
  private function parse_body()
  {
    // Leer el ouput del cuerpo dependiendo el verbo o petición
    switch (strtolower($this->r_type)) {
      case 'get':
        $this->body  = $_GET;
        $this->data  = $this->body;
        break;
      case 'post':
      case 'put':
      case 'delete':
      case 'headers':
      case 'options':
        // Accedemos al content type definido por la petición
        $contentType = isset($this->headers['CONTENT_TYPE']) ? $this->headers['CONTENT_TYPE'] : '';

        // Dependiendo el tipo de petición accedemos de forma diferente al cuerpo de la petición y la data en él
        if ($this->r_type === 'POST') {
          if ($contentType === 'application/json' || strpos($contentType, 'application/json') !== false) {
            // El cuerpo de la solicitud está en formato JSON
            $this->body   = file_get_contents('php://input');
            $this->data   = json_decode($this->body, true);
          } else if (strpos($contentType, 'multipart/form-data') !== false) {
            // El cuerpo de la solicitud está en formato form-data o similar
            $this->data   = $_POST;
          } else if (strpos($contentType, 'text/plain') !== false) {
            $this->body   = file_get_contents('php://input');
            $this->data   = json_decode($this->body, true);
          }
        } else if ($this->r_type === 'PUT') {
          // Cargamos todo el contenido del cuerpo de la solicitud
          $this->body     = file_get_contents('php://input');

          // Verificar el tipo de contenido del cuerpo de la solicitud
          if ($contentType === 'application/json' || strpos($contentType, 'application/json') !== false) {
            // El cuerpo de la solicitud está en formato JSON
            $this->data   = json_decode($this->body, true);
          } else if (strpos($contentType, 'multipart/form-data') !== false) {
            // El cuerpo de la solicitud está en formato form-data o similar
            // Puedes usar parse_str para analizar los datos de form-data en un array asociativo
            parse_str($this->body, $this->parsed);
            $this->data = $this->parsed;
          }
        } else {
          $this->body   = file_get_contents('php://input');
          $this->data   = json_decode($this->body, true);
        }

        // Anexamos todos los archivos encontrados
        $this->files  = isset($_FILES) ? $_FILES : [];
        break;
    }
  }

  /**
   * Contruye un array con toda la información de la petición
   *
   * @since 1.1.4
   * 
   * @return array
   */
  private function build_request()
  {
    return
    [
      'headers' => $this->headers,
      'type'    => $this->r_type,
      'data'    => $this->data,
      'files'   => $this->files
    ];
  }

  /**
   * Para escpecificar los verbos aceptados o autorizados en una ruta determinada.
   *
   * @param array $verbs
   * @return true
   */
  public function accept(array $verbs)
  {
    if (!in_array(strtoupper($this->r_type), array_map('strtoupper', $verbs))) {
      throw new BeeHttpException(
        'El verbo HTTP solicitado no está autorizado en esta ruta.', 
        403
      );
    }
    
    return true;
  }

  /**
   * Regresa el contenido de la petición
   *
   * @return array
   */
  public function get_request()
  {
    return $this->build_request();
  }
}
