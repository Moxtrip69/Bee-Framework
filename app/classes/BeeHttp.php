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
   * El tipo de petición realizada
   * al servidor en curso
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

  function __construct($class)
  {
    // Validar el contexto de la petición
    if ($class === 'apiController' && defined('DOING_API')) {
      $this->call         = 'api';
      $this->authenticate = bee_api_authentication();
    } elseif ($class === 'ajaxController' && defined('DOING_AJAX')) {
      $this->call = 'ajax';
    } else {
      throw new BeeHttpException(get_bee_message(0), 403); // 403
    }

    // Tipo de petición solicitada
    $this->r_type = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

    /**
     * @since 1.1.4
     */
    // Validar que se pase un verbo válido y aceptado
    if(!in_array(strtoupper($this->r_type), $this->accepted_verbs)) {
      throw new BeeHttpException(get_bee_message(1), 403); // 403
    }

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

    return $this;
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
    } else {
      $this->headers        = isset($_SERVER) ? $_SERVER : [];
    }
  }
  
  /**
   * Buscamos dentro de las cabeceras de la petición
   * si se han enviado alguna de las API keys de autenticación
   * para consumir los recursos de la API.
   * 
   * @since 1.1.4
   *
   * @return void
   */
  private function set_api_keys()
  {
    // En caso de existir custom headers para autenticación o consumo
    if ($this->apache_request === true) {
      $this->public_key  = isset($this->headers['auth_public_key']) ? $this->headers['auth_public_key'] : null;
      $this->private_key = isset($this->headers['auth_private_key']) ? $this->headers['auth_private_key'] : null;
    } else {
      $this->public_key  = isset($this->headers['HTTP_AUTH_PUBLIC_KEY']) ? $this->headers['HTTP_AUTH_PUBLIC_KEY'] : null;
      $this->private_key = isset($this->headers['HTTP_AUTH_PRIVATE_KEY']) ? $this->headers['HTTP_AUTH_PRIVATE_KEY'] : null;
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
   * Valida que el token CSRF enviado en la petición
   * sea correcto y válido para el usuario en curso
   *
   * @return void
   */
  private function validate_csrf()
  {
    if ($this->call === 'ajax' && in_array(strtolower($this->r_type), ['post', 'put', 'delete', 'headers', 'options']) && !Csrf::validate($this->csrf)) {
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
        $this->body  = $_POST;
        $this->data  = $this->body;
        $this->files = isset($_FILES) ? $_FILES : [];
        break;
      case 'put':
      case 'delete':
      case 'headers':
      case 'options':
        $this->body = file_get_contents('php://input');
        parse_str($this->body, $this->parsed);
        $this->data = $this->parsed;
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
   * Para determinar los verbos autorizados o disponibles en una determinada ruta.
   *
   * @param Array $verbs
   * @return true
   */
  public function accept(Array $verbs)
  {
    if (!in_array(strtoupper($this->r_type), array_map('strtoupper', $verbs))) {
      throw new BeeHttpException('El verbo HTTP utilizado en esta ruta no está autorizado.', 403);
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
