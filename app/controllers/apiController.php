<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de api
 */
class apiController extends Controller {

  /**
   * Versión de la API actual de Bee framework
   *
   * @var string
   */
  private $version = '1.0.0';

  /**
   * Instancia de la clase BeeHttp
   *
   * @var BeeHttp
   */
  private $http    = null;

  /**
   * La información de la petición completa
   * incluyendo el cuerpo de la petición, cabeceras,
   * files y el body RAW
   * 
   * @var array
   */
  private $req     = null;

  /**
   * Información ya formateada conteniendo el cuerpo
   * de la petición actual
   *
   * @var array
   */
  protected Array $data = [];

  /**
   * Array de archivos enviados en la petición
   * @since 1.5.6
   *
   * @var array
   */
  private $files   = [];

  function __construct()
  {
    // Prevenir el acceso no autorizado
    if (!defined('DOING_AJAX') && !defined('DOING_API')) {
      http_response_code(403);
      json_output(json_build(403));
    }

    // Procesamos la petición que está siendo mandada al servidor
    try {
      $this->http  = new BeeHttp(__CLASS__);
      $this->http->registerDomain('*'); // Recomiendo cambiar a un dominio específico por seguridad
      $this->http->process();
      $this->req   = $this->http->get_request();
      $this->data  = $this->req['data'];
      $this->files = $this->req['files'];
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));

    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, [], $e->getMessage()));

    }
  }
  
  function index()
  {
    json_output(json_build(204, [], sprintf('Bienvenido, versión de la API %s', $this->version)));
  }

  ///////////////////////////////////////////////////////
  ////////////// EJEMPLO BÁSICO DE USO //////////////////
  ///////////////////////////////////////////////////////
  function posts($id = null)
  {
    try {
      $this->http->accept(['get','post','put','delete']);
      $this->http->authenticate_request();

      switch ($this->req['type']) {
        case 'GET':
          if ($id !== null) {
            $this->get_post($id);
          } else {
            $this->get_posts();
          }
          break;
          
        case 'POST':
          $this->post_posts();
          break;

        case 'PUT':
          $this->put_post($id);
          break;

        case 'DELETE':
          $this->delete_posts($id);
          break;
      }
      
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));
    } catch (BeeJsonException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage(), $e->getErrorCode()));
    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, [], $e->getMessage()));
    }
  }

  private function get_posts()
  {
    $posts = Model::list('pruebas');
    json_output(json_build(200, $posts));
  }

  private function get_post($id)
  {
    $post = Model::list('pruebas', ['id' => $id], 1);

    if (empty($post)) {
      json_output(json_build(404, $post));
    }

    json_output(json_build(200, $post));
  }

  private function post_posts()
  {
    if (!check_posted_data(['nombre','titulo','contenido'], $this->data)) {
      throw new BeeJsonException('Parámetros faltantes en la petición.', 400, 'missing_params');
    }

    // Validar el tipo de parámetro solicitado
    if (!is_string($this->data['nombre'])) {
      throw new BeeJsonException('Parámetro nombre debe ser string.', 400, 'param_error');
    }

    if (strlen($this->data['nombre']) < 5) {
      throw new BeeJsonException('Parámetro nombre debe ser mayor a 5 caracteres.', 400, 'param_error');
    }

    if (!is_string($this->data['nombre'])) {
      throw new BeeJsonException('Parámetro nombre debe ser string.', 400, 'param_error');
    }

    // Más validaciones y sanitizaciones de input debe ser realizada
    // solo es un ejemplo general de como utilizar el área de la API

    $nombre    = sanitize_input($this->data['nombre'], true);
    $titulo    = sanitize_input($this->data['titulo'], true);
    $contenido = sanitize_input($this->data['contenido'], true);
    $data      =
    [
      'nombre'    => $nombre,
      'titulo'    => $titulo,
      'contenido' => $contenido,
      'creado'    => now()
    ];

    if (!$id = Model::add('pruebas', $data)) {
      throw new BeeJsonException(get_bee_message('not_added'), 400, 'db_error');
    }

    $post = Model::list('pruebas', ['id' => $id], 1);

    json_output(json_build(201, $post, 'Nuevo post agregado con éxito.'));
  }

  private function put_post($id)
  {
    try {
      if (!check_posted_data(['titulo','contenido','nombre'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      $id        = sanitize_input($id);
      $nombre    = sanitize_input($this->data['nombre']);
      $titulo    = sanitize_input($this->data['titulo']);
      $contenido = sanitize_input($this->data['contenido']);

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
      json_output(json_build(400, [], $e->getMessage()));
    }
  }

  private function delete_posts($id = null)
  {
    if (!$post = Model::list('pruebas', ['id' => $id], 1)) {
      throw new BeeJsonException(get_bee_message('not_found'), 400, 'not_found');
    }

    if (!Model::remove('pruebas', ['id' => $id])) {
      throw new BeeJsonException(get_bee_message('not_deleted'), 400, 'not_deleted');
    }
    
    json_output(json_build(200, $post, 'Post borrado con éxito.'));
  }

  function upload()
  {
    try {
      $this->http->accept(['POST']);
      $this->http->authenticate_request();

      if (!isset($this->files['imagen'])) {
        throw new Exception('Envía una imagen válida.');
      }

      $imagen   = $this->files['imagen'];
      $ext      = pathinfo($imagen['name'], PATHINFO_EXTENSION);
      $new_name = sprintf('%s.%s', generate_filename(), $ext);
      $uploaded = move_uploaded_file($imagen['tmp_name'], UPLOADS . $new_name);

      if (!$uploaded) {
        throw new Exception('Hubo un error al subir la imagen.');
      }

      // Procesar la imagen
      json_output(json_build(200, $imagen, sprintf('Imagen %s subida con éxito.', $new_name)));
      
    } catch (BeeHttpException $e) {
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));
    } catch (BeeJsonException $e) {
      json_output(json_build($e->getStatusCode(), [], $e->getMessage(), $e->getErrorCode()));
    } catch (Exception $e) {
      json_output(json_build(400, [], $e->getMessage()));
    }
  }

  function form_builder()
  {
    try {
      $this->http->accept(['get','post','put','delete']);
      $this->http->authenticate_request();

      json_output(json_build(200, array_merge($this->data, $this->files), 'Información recibida con éxito.'));
      
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));
    } catch (BeeJsonException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage(), $e->getErrorCode()));
    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, [], $e->getMessage()));
    }
  }
}