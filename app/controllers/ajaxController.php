<?php 

class ajaxController extends Controller {

  /**
   * El tipo de petición realizada
   * al servidor en curso
   *
   * @var string
   */
  private $r_type = null;

  /**
   * Hook solicitado para la petición
   *
   * @var string
   * 
   * @deprecated 1.1.4
   */
  private $hook   = null;

  /**
   * Tipo de acción a realizar en ajax
   *
   * @var string
   * 
   * @deprecated 1.1.4
   */
  private $action = null;

  /**
   * Token csrf de la sesión del usuario que solicita la petición
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
   * Valor que se deberá proporcionar como hook para
   * aceptar una petición entrante
   *
   * @var string
   * @deprecated 1.1.4
   */
  private $hook_name        = 'bee_hook'; // Si es modificado, actualizar el valor en la función core insert_inputs()
  
  /**
   * parámetros que serán requeridos en TODAS las peticiones pasadas a ajaxController
   * si uno de estos no es proporcionado la petición fallará
   *
   * @var array
   * @deprecated 1.1.4
   */
  private $required_params  = ['hook', 'action'];

  /**
   * Posibles verbos o acciones disponibles para nuestra petición
   * 
   * Actualizados
   * @since 1.1.4
   *
   * @var array
   */
  private $accepted_actions = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEADERS'];

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

  function __construct()
  {
    // Prevenir el acceso no autorizado
    if (!defined('DOING_AJAX') && !defined('DOING_API')) die();

    // Tipo de petición solicitada
    $this->r_type = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

    /**
     * @deprecated 1.1.4
     */
    // // Validar que hook exista y sea válido
    // if ($this->hook !== $this->hook_name) {
    //   http_response_code(403);
    //   json_output(json_build(403, null, 'Hook no autorizado.'));
    // }

    /**
     * @since 1.1.4
     */
    // Validar que se pase un verbo válido y aceptado
    if(!in_array(strtoupper($this->r_type), $this->accepted_actions)) {
      http_response_code(403);
      json_output(json_build(403, null, 'Acción no autorizada.'));
    }

    // Almacenando y determinando las cabeceras recibidas
    $this->get_headers();

    // Parsing del cuerpo de la petición
    $this->parse_data();

    // Parámetros adicionales
    /**
     * @deprecated 1.1.4
     */
    $this->hook   = isset($this->data['hook']) ? $this->data['hook'] : null;

    /**
     * @deprecated 1.1.4
     */
    $this->action = isset($this->data['action']) ? $this->data['action'] : null;
    $this->csrf   = isset($this->data['csrf']) ? $this->data['csrf'] : null;
   
    /**
     * @deprecated 1.1.4
     */
    // Validación de que todos los parámetros requeridos son proporcionados
    // foreach ($this->required_params as $param) {
    //   if(!isset($this->data[$param])) {
    //     http_response_code(403);
    //     json_output(json_build(403, null, 'Parámetros incompletos.'));
    //   }
    // }

    // Validar de la petición post / put / delete el token csrf
    if (in_array(strtolower($this->r_type), ['post', 'put', 'delete', 'headers', 'options']) && !Csrf::validate($this->csrf)) {
      http_response_code(401);
      json_output(json_build(401, null, 'Autorización no válida.'));
    }
  }

  function index()
  {
    http_response_code(404);
    json_output(json_build(404, null, 'Ruta no encontrada.'));
  }

  private function get_headers()
  {
    $apache_request = false;

    if (function_exists('apache_request_headers')) {
      $apache_request = true;
      $this->headers  = apache_request_headers();
    } else {
      $this->headers = isset($_SERVER) ? $_SERVER : [];
    }

    // En caso de existir custom headers para autenticación o consumo
    if ($apache_request === true) {
      $this->public_key  = isset($this->headers['auth_public_key']) ? $this->headers['auth_public_key'] : null;
      $this->private_key = isset($this->headers['auth_private_key']) ? $this->headers['auth_private_key'] : null;
    } else {
      $this->public_key  = isset($this->headers['HTTP_AUTH_PUBLIC_KEY']) ? $this->headers['HTTP_AUTH_PUBLIC_KEY'] : null;
      $this->private_key = isset($this->headers['HTTP_AUTH_PRIVATE_KEY']) ? $this->headers['HTTP_AUTH_PRIVATE_KEY'] : null;
    }
  }

  /**
   * Parsea el contenido del cuerpo de la petición con
   * base al verbo o tipo de petición realizada
   * @since 1.1.4
   *
   * @return void
   */
  private function parse_data()
  {
    // Leer el ouput del cuerpo dependiendo el verbo o petición
    switch (strtolower($this->r_type)) {
      case 'get':
        $this->body = $_GET;
        $this->data = $this->body;
        break;
      case 'post':
        $this->body  = $_POST;
        $this->data  = $this->body;
        $this->files = !isset($_FILES) && !empty($_FILES) ? $_FILES : [];
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
   * Realiza una prueba de conexióna la base de datos
   * @since 1.1.4
   *
   * @return void
   */
  function db_test()
  {
    try {
      $db = Db::connect(true);
      json_output(json_build(200, null, sprintf('Conexión realizada con éxito a la base de datos <b>%s</b>.', is_local() ? LDB_NAME : DB_NAME)));
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas bee framework
   * @since 1.1.4
   *
   * @return void
   */
  function test()
  {
    try {
      json_output(json_build(200, null, 'Prueba de AJAX realizada con éxito.'));
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas para vuejs
   * @since 1.1.4
   *
   * @return void
   */
  function test_posts()
  {
    try {
      $posts = Model::list('pruebas');
      json_output(json_build(200, $posts));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas para cargar un post de la base de datos
   *
   * @return void
   */
  function test_get_post()
  {
    try {
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      if (!$post = Model::list('pruebas', ['id' => $this->data['id']], 1)) {
        throw new Exception(get_bee_message('not_found'));
      }

      json_output(json_build(200, $post));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas para agregar un post a la base de datos
   *
   * @return void
   */
  function test_add_post()
  {
    try {
      if (!check_posted_data(['titulo','contenido','nombre'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      if (!Auth::validate()) {
        throw new Exception(get_bee_message('auth'));
      }

      $id        = null;
      $nombre    = clean($this->data['nombre']);
      $titulo    = clean($this->data['titulo']);
      $contenido = clean($this->data['contenido']);

      $data =
      [
        'nombre'    => $nombre,
        'titulo'    => $titulo,
        'contenido' => $contenido,
        'creado'    => now()
      ];

      if (!$id = Model::add('pruebas', $data)) {
        throw new Exception(get_bee_message('not_added'));
      }

      $post = Model::list('pruebas', ['id' => $id], 1);
      
      json_output(json_build(201, $post, get_bee_message('added')));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas para actualizar un post de la base de datos
   *
   * @return void
   */
  function test_update_post()
  {
    try {
      if (!check_posted_data(['id','titulo','contenido','nombre'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      $id        = clean($this->data['id']);
      $nombre    = clean($this->data['nombre']);
      $titulo    = clean($this->data['titulo']);
      $contenido = clean($this->data['contenido']);

      if (!$post = Model::list('pruebas', ['id' => $id], 1)) {
        throw new Exception(get_bee_message('not_found'));
      }

      $data =
      [
        'nombre'    => $nombre,
        'titulo'    => $titulo,
        'contenido' => $contenido
      ];

      if (!Model::update('pruebas', ['id' => $id], $data)) {
        throw new Exception(get_bee_message('not_updated'));
      }

      $post = Model::list('pruebas', ['id' => $id], 1);
      
      json_output(json_build(200, $post, get_bee_message('updated')));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función de pruebas para borrar un post de la base de datos
   * @since 1.1.4
   *
   * @return void
   */
  function test_delete_post()
  {
    try {
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      if (!$post = Model::list('pruebas', ['id' => $this->data['id']], 1)) {
        throw new Exception(get_bee_message('not_found'));
      }

      if (!Model::remove('pruebas', ['id' => $post['id']])) {
        throw new Exception(get_bee_message('not_deleted'));
      }
      
      json_output(json_build(200, $post, 'Post borrado con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }
}