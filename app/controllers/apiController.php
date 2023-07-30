<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de api
 */
class apiController extends Controller {

  /**
   * Instancia de la clase BeeHttp
   *
   * @var BeeHttp
   */
  private $http = null;

  /**
   * La información de la petición completa
   * incluyendo el cuerpo de la petición, cabeceras,
   * files y el body RAW
   * 
   * @var array
   */
  private $req  = null;

  /**
   * Información ya formateada conteniendo el cuerpo
   * de la petición actual
   *
   * @var array
   */
  private $data = [];

  /**
   * Array de archivos enviados en la petición
   * @since 1.5.6
   *
   * @var array
   */
  private $files = [];

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
      $this->req   = $this->http->get_request();
      $this->data  = $this->req['data'];
      $this->files = $this->req['files'];
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), null, $e->getMessage()));
    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, null, $e->getMessage()));
    }
  }
  
  function index()
  {
    http_response_code(404);
    json_output(json_build(404, null, 'Ruta no encontrada.'));
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
      json_output(json_build($e->getStatusCode(), null, $e->getMessage()));
    } catch (BeeJsonException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), null, $e->getMessage(), $e->getErrorCode()));
    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, null, $e->getMessage()));
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
      json_output(json_build(400, null, $e->getMessage()));
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
      json_output(json_build($e->getStatusCode(), null, $e->getMessage()));
    } catch (BeeJsonException $e) {
      json_output(json_build($e->getStatusCode(), null, $e->getMessage(), $e->getErrorCode()));
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  ///////////////////////////////////////////////////////
  ////////////// CLASE EN VIVO #2 #3   //////////////////
  ///////////////////////////////////////////////////////
  function juegos($id = null)
  {
    try {
      $this->http->accept(['get','post','put','delete']);
      $this->http->authenticate_request();

      switch ($this->req['type']) {
        case 'GET':
          if ($id !== null) {
            $this->get_juego($id);
          } else {
            $this->get_juegos();
          }
          break;
          
        case 'POST':
          $this->post_juego();
          break;

        case 'PUT':
          $this->put_juego($id);
          break;

        case 'DELETE':
          $this->detele_juego($id);
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

  private function get_juego($id)
  {
    // Validación de parámetro id
    if (!is_numeric($id)) {
      throw new BeeJsonException('Parámetro ID no válido.', 400, 'id_invalid');
    }

    // Query a la base de datos
    $sql = 
    'SELECT 
    j.* ,
    (SELECT COUNT(id) FROM votos WHERE j.id = id_juego) AS votos
    FROM juegos j
    WHERE j.id = :id 
    LIMIT 1';
    $row = Model::query($sql, ['id' => $id]);

    if (empty($row)) {
      json_output(json_build(404, [], 'No existe el juego solicitado.'));
    }

    json_output(json_build(200, $row[0]));
  }

  private function get_juegos()
  {
    $sql = 
    'SELECT 
    j.* ,
    (SELECT COUNT(id) FROM votos WHERE j.id = id_juego) AS votos
    FROM juegos j
    ORDER BY j.id';

    $rows = Model::query($sql);

    json_output(json_build(200, !empty($rows) ? $rows : []));
  }

  private function post_juego()
  {
    // Verificar que estemos recibiendo todos los parámetros
    if (!check_posted_data(['titulo','plataforma','precio'], $this->data)) {
      throw new BeeJsonException('Parámetros faltantes en la petición.', 400, 'missing_params');
    }

    // Definición
    $titulo     = $this->data['titulo'];
    $plataforma = $this->data['plataforma'];
    $precio     = (float) $this->data['precio'];

    // Validaciones para: título
    if (!is_string($titulo)) {
      throw new BeeJsonException(
        'Parámetro título debe ser un string válido.', 
        400,
        'invalid_type'
      );
    }

    if (strlen($titulo) < 5) {
      throw new BeeJsonException(
        'Parámetro título debe ser mayor a 5 caracteres.', 
        400,
        'invalid_format'
      );
    }
    
    // Validaciones para: plataforma
    if (!is_string($plataforma)) {
      throw new BeeJsonException(
        'Parámetro plataforma debe ser un string válido.', 
        400,
        'invalid_type'
      );
    }

    // Validaciones para: precio
    if (!is_float($precio)) {
      throw new BeeJsonException(
        'Parámetro precio debe ser un número válido.', 
        400,
        'invalid_type'
      );
    }

    // Validaciones para: imagen
    if (!isset($this->files['imagen'])) {
      throw new BeeJsonException(
        'Parámetro imagen debe ser un archivo de imagen válido.', 
        400,
        'missing_image'
      );
    }

    // Definimos la imagen
    $imagen = $this->files['imagen'];

    if ($imagen['error'] !== UPLOAD_ERR_OK) {
      throw new BeeJsonException(
        'Hubo un problema al subir el archivo.', 
        400,
        'upload_error'
      );
    }

    // Datos del archivo
    $imagen_nombre = $imagen['name'];
    $imagen_tmp    = $imagen['tmp_name'];
    $imagen_tipo   = $imagen['type'];
    $imagen_ext    = pathinfo($imagen_nombre, PATHINFO_EXTENSION);
    $nuevo_nombre  = sprintf('%s.%s', generate_filename(), $imagen_ext);

    // Lista de tipos MIME válidos para imágenes
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    $ext_permitidas   = ['jpg', 'jpeg', 'png', 'gif'];

    // Verificar si la extensión y el tipo MIME están permitidos
    if (!in_array($imagen_tipo, $tipos_permitidos) || !in_array($imagen_ext, $ext_permitidas)) {
      throw new BeeJsonException(
        'El formato de la imagen es inválido.', 
        400,
        'invalid_format'
      );
    }

    // Mover el archivo
    if (!move_uploaded_file($imagen_tmp, UPLOADS . $nuevo_nombre)) {
      throw new BeeJsonException(
        'Hubo un problema al mover el archivo.', 
        400,
        'upload_error'
      );
    }

    // Sanitización para guardado
    $data =
    [
      'titulo'     => $titulo,
      'plataforma' => $plataforma,
      'precio'     => $precio,
      'imagen'     => $nuevo_nombre,
      'creado'     => now()
    ];

    if (!$id = Model::add('juegos', $data)) {
      unlink(UPLOADS . $nuevo_nombre); // borrar la imagen
      throw new BeeJsonException(
        'El registro no se ha insertado.', 
        400,
        'db_error'
      );
    }

    $juego = Model::list('juegos', ['id' => $id], 1);

    json_output(json_build(201, $juego, 'Nuevo juego agregado con éxito.'));
  }

  private function put_juego($id = null)
  {
    if ($id === null) {
      throw new BeeJsonException(
        'Parámetro ID faltante en la petición.', 
        400, 
        'missing_params'
      );
    }

    // Verificar que estemos recibiendo todos los parámetros
    if (!check_posted_data(['titulo','plataforma','precio'], $this->data)) {
      throw new BeeJsonException(
        'Parámetros faltantes en la petición.', 
        400, 
        'missing_params'
      );
    }

    // Definición
    $titulo     = $this->data['titulo'];
    $plataforma = $this->data['plataforma'];
    $precio     = (float) $this->data['precio'];

    // Validar que exista el registro
    if (!Model::list('juegos', ['id' => $id], 1)) {
      throw new BeeJsonException(
        'No existe el juego solicitado.', 
        404, 
        'not_found'
      );
    }

    // Validaciones para: título
    if (!is_string($titulo)) {
      throw new BeeJsonException(
        'Parámetro título debe ser un string válido.', 
        400,
        'invalid_type'
      );
    }

    if (strlen($titulo) < 5) {
      throw new BeeJsonException(
        'Parámetro título debe ser mayor a 5 caracteres.', 
        400,
        'invalid_format'
      );
    }
    
    // Validaciones para: plataforma
    if (!is_string($plataforma)) {
      throw new BeeJsonException(
        'Parámetro plataforma debe ser un string válido.', 
        400,
        'invalid_type'
      );
    }

    // Validaciones para: precio
    if (!is_float($precio)) {
      throw new BeeJsonException(
        'Parámetro precio debe ser un número válido.', 
        400,
        'invalid_type'
      );
    }

    // Sanitización para guardado
    $data =
    [
      'titulo'     => $titulo,
      'plataforma' => $plataforma,
      'precio'     => $precio
    ];

    if (!Model::update('juegos', ['id' => $id], $data)) {
      throw new BeeJsonException(
        'El registro no se ha actualizado.', 
        400,
        'db_error'
      );
    }

    $juego = Model::list('juegos', ['id' => $id], 1);

    json_output(json_build(200, $juego, 'Juego actualizado con éxito.'));
  }

  private function detele_juego($id = null)
  {
    if ($id === null) {
      throw new BeeJsonException(
        'Parámetro ID faltante en la petición.', 
        400, 
        'missing_params'
      );
    }

    // Validar que exista el registro
    if (!$juego = Model::list('juegos', ['id' => $id], 1)) {
      throw new BeeJsonException(
        'No existe el juego solicitado.', 
        404, 
        'not_found'
      );
    }

    // Borrar de la base de datos
    if (!Model::remove('juegos', ['id' => $id])) {
      throw new BeeJsonException(
        'El registro no se ha borrado.', 
        400, 
        'db_error'
      );
    }
    
    json_output(json_build(200, $juego, 'Juego borrado con éxito.'));
  }

  function votar($id = null)
  {
    try {
      $this->http->accept(['post']);
      $this->http->authenticate_request();

      if ($id === null) {
        throw new BeeJsonException(
          'Parámetro ID faltante en la petición.', 
          400, 
          'missing_params'
        );
      }
  
      // Validar que exista el registro
      if (!Model::list('juegos', ['id' => $id], 1)) {
        throw new BeeJsonException(
          'No existe el juego solicitado.', 
          404, 
          'not_found'
        );
      }
  
      // Validar que no exista ya un voto con base a la IP o usuario
  
      // Agregar el voto en la base de datos
      $data =
      [
        'id_juego' => $id,
        'ip'       => get_user_ip()
      ];
  
      // Agregar el voto en la base de datos
      if (!Model::add('votos', $data)) {
        throw new BeeJsonException(
          'El registro no se ha insertado.', 
          400, 
          'db_error'
        );
      }
  
      $juego = Model::list('juegos', ['id' => $id], 1);
      
      json_output(json_build(201, $juego, 'Voto agregado con éxito.'));

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

  function imagen($id = null)
  {
    try {
      $this->http->accept(['post']);
      $this->http->authenticate_request();

      if ($id === null) {
        throw new BeeJsonException(
          'Parámetro ID faltante en la petición.', 
          400, 
          'missing_params'
        );
      }
  
      // Validar que exista el registro
      if (!$juego = Model::list('juegos', ['id' => $id], 1)) {
        throw new BeeJsonException(
          'No existe el juego solicitado.', 
          404, 
          'not_found'
        );
      }

      // Almacenar imagen anterior
      $imagen_vieja = $juego['imagen'];
  
      // Validaciones para: imagen
      if (!isset($this->files['imagen'])) {
        throw new BeeJsonException(
          'Parámetro imagen debe ser un archivo de imagen válido.', 
          400,
          'missing_image'
        );
      }

      // Definimos la imagen
      $imagen = $this->files['imagen'];

      if ($imagen['error'] !== UPLOAD_ERR_OK) {
        throw new BeeJsonException(
          'Hubo un problema al subir el archivo.', 
          400,
          'upload_error'
        );
      }

      // Datos del archivo
      $imagen_nombre = $imagen['name'];
      $imagen_tmp    = $imagen['tmp_name'];
      $imagen_tipo   = $imagen['type'];
      $imagen_ext    = pathinfo($imagen_nombre, PATHINFO_EXTENSION);
      $nuevo_nombre  = sprintf('%s.%s', generate_filename(), $imagen_ext);

      // Lista de tipos MIME válidos para imágenes
      $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
      $ext_permitidas   = ['jpg', 'jpeg', 'png', 'gif'];

      // Verificar si la extensión y el tipo MIME están permitidos
      if (!in_array($imagen_tipo, $tipos_permitidos) || !in_array($imagen_ext, $ext_permitidas)) {
        throw new BeeJsonException(
          'El formato de la imagen es inválido.', 
          400,
          'invalid_format'
        );
      }

      // Mover el archivo
      if (!move_uploaded_file($imagen_tmp, UPLOADS . $nuevo_nombre)) {
        throw new BeeJsonException(
          'Hubo un problema al mover el archivo.', 
          400,
          'upload_error'
        );
      }

      // Actualizar el registro
      $data =
      [
        'imagen' => $nuevo_nombre
      ];
      
      if (!Model::update('juegos', ['id' => $id], $data)) {
        unlink(UPLOADS . $nuevo_nombre);
        throw new BeeJsonException(
          'El registro no se ha actualizado.', 
          400,
          'db_error'
        );
      }

      // Borrar imagen anterior
      if (is_file(UPLOADS . $imagen_vieja)) {
        unlink(UPLOADS . $imagen_vieja);
      }
  
      $juego = Model::list('juegos', ['id' => $id], 1);
      
      json_output(json_build(200, $juego, 'Imagen actualizada con éxito.'));

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