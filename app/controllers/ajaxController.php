<?php 

class ajaxController extends Controller {

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
  protected Array $data = [];

  /**
   * Archivos enviados al servidor en la petición
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
        $post = Model::list('posts', ['id' => $id], 1); // cargar el post

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

  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////// INSERTA TUS MÉTODOS DESPUÉS DE ESTE BLOQUE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
}