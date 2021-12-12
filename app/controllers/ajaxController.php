<?php 

class ajaxController extends Controller {

  /**
   * La petición del servidor
   *
   * @var string
   */
  private $r_type = null;

  /**
   * Hook solicitado para la petición
   *
   * @var string
   */
  private $hook   = null;

  /**
   * Tipo de acción a realizar en ajax
   *
   * @var string
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
   * Posibles verbos o acciones a pasar para nuestra petición
   *
   * @var array
   */
  private $accepted_actions = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEADERS'];

  /**
   * Cabeceras de la petición entrante
   *
   * @var array
   */
  private $headers          = [];

  /**
   * API Keys recibidas para consumir ciertos recursos
   * solo en caso de ser necesarias
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
    /**
    200 OK
    201 Created
    300 Multiple Choices
    301 Moved Permanently
    302 Found
    304 Not Modified
    307 Temporary Redirect
    400 Bad Request
    401 Unauthorized
    403 Forbidden
    404 Not Found
    410 Gone
    500 Internal Server Error
    501 Not Implemented
    503 Service Unavailable
    550 Permission denied
    */
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

  ///////////////////////////////////////////////////////
  ///////////////////// PROYECTO DEMO ///////////////////
  ///////////////////////////////////////////////////////
  function bee_add_movement()
  {
    try {
      $mov              = new movementModel();
      $mov->type        = $_POST['type'];
      $mov->description = $_POST['description'];
      $mov->amount      = (float) $_POST['amount'];
      if(!$id = $mov->add()) {
        json_output(json_build(400, null, 'Hubo error al guardar el registro'));
      }
  
      // se guardó con éxito
      $mov->id = $id;
      json_output(json_build(201, $mov->one(), 'Movimiento agregado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_get_movements()
  {
    try {
      $movements          = new movementModel;
      $movs               = $movements->all_by_date();

      $taxes              = (float) get_option('taxes') < 0 ? 16 : get_option('taxes');
      $use_taxes          = get_option('use_taxes') === 'Si' ? true : false;
      
      $total_movements    = $movs[0]['total'];
      $total              = $movs[0]['total_incomes'] - $movs[0]['total_expenses'];
      $subtotal           = $use_taxes ? $total / (1.0 + ($taxes / 100)) : $total;
      $taxes              = $subtotal * ($taxes / 100);
      
      $calculations       = [
        'total_movements' => $total_movements,
        'subtotal'        => $subtotal,
        'taxes'           => $taxes,
        'total'           => $total
      ];

      $data = get_module('movements', ['movements' => $movs, 'cal' => $calculations]);
      json_output(json_build(200, $data));
    } catch(Exception $e) {
      json_output(json_build(400, $e->getMessage()));
    }

  }

  function bee_delete_movement()
  {
    try {
      $mov     = new movementModel();
      $mov->id = $_POST['id'];

      if(!$mov->delete()) {
        json_output(json_build(400, null, 'Hubo error al borrar el registro'));
      }

      json_output(json_build(200, null, 'Movimiento borrado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_update_movement()
  {
    try {
      $movement     = new movementModel;
      $movement->id = $_POST['id'];
      $mov          = $movement->one();

      if(!$mov) {
        json_output(json_build(400, null, 'No existe el movimiento'));
      }

      $data = get_module('updateForm', $mov);
      json_output(json_build(200, $data));
    } catch(Exception $e) {
      json_output(json_build(400, $e->getMessage()));
    }
  }

  function bee_save_movement()
  {
    try {
      $mov              = new movementModel();
      $mov->id          = $_POST['id'];
      $mov->type        = $_POST['type'];
      $mov->description = $_POST['description'];
      $mov->amount      = (float) $_POST['amount'];
      if(!$mov->update()) {
        json_output(json_build(400, null, 'Hubo error al guardar los cambios'));
      }
  
      // se guardó con éxito
      json_output(json_build(200, $mov->one(), 'Movimiento actualizado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_save_options()
  {
    $options =
    [
      'use_taxes' => $_POST['use_taxes'],
      'taxes'     => (float) $_POST['taxes'],
      'coin'      => $_POST['coin']
    ];

    foreach ($options as $k => $option) {
      try {
        if(!$id = optionModel::save($k, $option)) {
          json_output(json_build(400, null, sprintf('Hubo error al guardar la opción %s', $k)));
        }
    
        
      } catch (Exception $e) {
        json_output(json_build(400, null, $e->getMessage()));
      }
    }

    // se guardó con éxito
    json_output(json_build(200, null, 'Opciones actualizadas con éxito'));
  }
  ///////////////////////////////////////////////////////
  /////////////// TERMINA PROYECTO DEMO /////////////////
  ///////////////////////////////////////////////////////
}