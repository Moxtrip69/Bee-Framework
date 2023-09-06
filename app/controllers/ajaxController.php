<?php 

class ajaxController extends Controller implements ControllerInterface {

  function __construct()
  {
    parent::__construct('ajax');
  }

  function index()
  {
    http_response_code(404);
    json_output(json_build(404, null, 'Ruta no encontrada.'));
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////// FUNCIONALIDADES DE PRUEBA | PUEDES BORRAR TODO ESTO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////

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
      json_output(json_build(200, null, sprintf('Conexión realizada con éxito a la base de datos <b>%s</b>.', is_local() ? LDB_NAME : add_ellipsis(DB_NAME, 5))));
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
   * Función de pruebas para Vuejs
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

  /**
   * Carga todos los memes de los resultados de búsqueda de imágenes de google
   *
   * @return void
   */
  function load_memes()
  {
    try {
      if (!check_posted_data(['page','query'], $this->data)) {
        throw new Exception('Parámetros faltantes en la petición.');
      }

      // Número de página de resultados (por defecto, la primera página)
      $page              = intval($this->data['page']);
      $searchQuery       = $this->data['query'];
      $google_search_url = "https://www.google.com/search?q=" . urlencode($searchQuery) . "&tbm=isch&start=" . ($page - 1) * 20;
      $response          = file_get_contents($google_search_url);


      // Analiza la página de resultados de Google Images para extraer las URLs de las imágenes
      preg_match_all('/<img.*?src=["\'](https:\/\/[^"\']+)/', $response, $matches);

      // Elimina las imágenes duplicadas
      $imageUrls = array_unique($matches[1]);

      json_output(json_build(200, $imageUrls));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function save()
  {
    try {
      if (!check_posted_data(['titulo','contenido','id'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      // Sanitización del input del usuario
      array_map('sanitize_input', $this->data);
      $id        = empty($this->data['id']) ? null : $this->data['id'];
      $titulo    = $this->data['titulo'];
      $contenido = $this->data['contenido'];
      $permalink = normalize_string($titulo);

      // Inicializar el array de información a insertar o actualizar
      $data =
      [
        'tipo'       => 'noticia',
        'id_padre'   => 0,
        'id_usuario' => 1,
        'id_ref'     => 0,
        'titulo'     => $titulo,
        'permalink'  => $permalink,
        'contenido'  => $contenido,
        'mime_type'  => 'plain-text'
      ];

      // Verificar si ya existe el post en la base de datos
      if (!Model::list('posts', ['id' => $id], 1)) {
        // Anexar data de nuevo registro
        $data = array_merge($data, ['creado' => now(), 'status' => 'draft']);
        $id   = Model::add('posts', $data);

      } else {
        // En caso que ya exista, actualizar la información
        Model::update('posts', ['id' => $id], $data);

      }
      
      $post = Model::list('posts', ['id' => $id], 1); // cargar el post
      json_output(json_build(200, $post, 'Noticia guardada con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function generar_notificacion()
  {
    try {
      // Inicializar el array de información a insertar o actualizar
      $placeholders =
      [
        'Nuevo correo electrónico',
        'Nueva venta recibida',
        'Nuevo reporte generado',
        'Nuevo anticipo solicitado'
      ];
      $notificacion = sprintf('%s #%s', $placeholders[rand(0, count($placeholders) - 1)], random_password(6, 'numeric'));

      $data         =
      [
        'tipo'       => 'notificacion',
        'id_padre'   => 0,
        'id_usuario' => 0,
        'id_ref'     => 0,
        'titulo'     => $notificacion,
        'status'     => 'pendiente',
        'creado'     => now()
      ];

      // Verificar si ya existe el post en la base de datos
      $id   = Model::add('posts', $data);
      $post = Model::list('posts', ['id' => $id], 1); // cargar el post / notificación

      json_output(json_build(201, $post, $notificacion));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function sse()
  {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    // Cargar las notificaciones que sean nuevas
    $notificaciones = Model::list('posts', ['tipo' => 'notificacion']);
    $notificaciones = $notificaciones === false ? [] : $notificaciones;
    $totales        = !empty($notificaciones) ? count($notificaciones) : 0;
    $pendientes     = 0;
    $cargadas       = 0;
    $vistas         = 0;

    // Actualizar status de cada notificación
    if (!empty($notificaciones)) {
      foreach ($notificaciones as $notificacion) {
        switch ($notificacion['status']) {
          case 'pendiente':
            $pendientes++;
            Model::update('posts', ['id' => $notificacion['id']], ['status' => 'cargada']);
            break;

          case 'cargada':
            $cargadas++;
            break;
          
          case 'vista':
            $vistas++;
            break;
        }
      }
    }

    $data =
    [
      'totales'        => $totales, 
      'pendientes'     => $pendientes, 
      'cargadas'       => $cargadas,
      'vistas'         => $vistas, 
      'notificaciones' => $notificaciones
    ];

    $payload = json_build(200, $data);

    // Envía la notificación al cliente
    echo "data: $payload\n\n";
    flush();
  }

  function actualizar_notificacion()
  {
    try {
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }

      // Sanitización del input del usuario
      array_map('sanitize_input', $this->data);
      $id        = empty($this->data['id']) ? null : $this->data['id'];

      // Verificar si existe la notificación en la base de datos
      if (!$notificacion = Model::list('posts', ['id' => $id], 1)) {
        throw new Exception('No existe la notificación en la base de datos.');
      }

      Model::update('posts', ['id' => $id], ['status' => 'vista']);
      
      $post = Model::list('posts', ['id' => $id], 1); // cargar el post
      json_output(json_build(200, $post, 'Notificación actualizada.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////// INSERTA TUS MÉTODOS DESPUÉS DE ESTE BLOQUE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
}