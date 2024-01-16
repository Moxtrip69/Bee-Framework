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

  function cargar_reportes()
  {
    $sql = 'SELECT * FROM posts WHERE tipo = "reporte" ORDER BY id DESC';
    $reportes = Model::query($sql);
    
    json_output(json_build(200, $reportes));
  }

  function levantar_reporte()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['nombre','email','problema'], $this->data)) {
        throw new Exception('Completa el formulario por favor.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $folio    = uniqid();
      $nombre   = $this->data['nombre'];
      $email    = $this->data['email'];
      $problema = $this->data['problema'];
      $imagen   = $this->files['imagen'];

      // Validar imagen en general
      if ($imagen['error'] !== 0) {
        throw new Exception('Selecciona una imagen válida para continuar.');
      }

      // Guardar imagen en el servidor
      $tmp     = $imagen['tmp_name'];
      $ext     = pathinfo($imagen['name'], PATHINFO_EXTENSION);
      $nImagen = generate_filename() . '.' . $ext;

      if (!move_uploaded_file($tmp, UPLOADS . $nImagen)) {
        throw new Exception('Hubo un error al subir la imagen.');
      }

      $data     =
      [
        'tipo'       => 'reporte',
        'titulo'     => sprintf('Reporte #%s', $folio),
        'permalink'  => $folio,
        'contenido'  => sprintf('Nombre: %s, Correo electrónico: %s, Reporte del problema: %s', $nombre, $email, $problema),
        'mime_type'  => $nImagen,
        'status'     => 'pendiente',
        'creado'     => now()
      ];

      // Verificar si ya existe el post en la base de datos
      $id      = Model::add('posts', $data);
      $reporte = Model::list('posts', ['id' => $id], 1); // cargar el post

      json_output(json_build(201, $reporte, 'Nuevo reporte levantado con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function resolver_reporte()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('El ID del reporte no es válido.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $id = $this->data['id'];

      if (!$reporte = Model::list('posts', ['id' => $id], 1)) {
        throw new Exception('No existe el reporte en la base de datos.');
      }

      // Verificar el estado del reporte
      if ($reporte['status'] !== 'pendiente') {
        throw new Exception('El estado del reporte no es válido.');
      }

      // Actualizar
      Model::update('posts', ['id' => $id], ['status' => 'resuelto']);

      // Cargar de nuevo
      $reporte = Model::list('posts', ['id' => $id], 1); // cargar el post

      json_output(json_build(200, $reporte, sprintf('Reporte #%s resuelto con éxito.', $reporte['permalink'])));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function pendiente_reporte()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('El ID del reporte no es válido.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $id = $this->data['id'];

      if (!$reporte = Model::list('posts', ['id' => $id], 1)) {
        throw new Exception('No existe el reporte en la base de datos.');
      }

      // Verificar el estado del reporte
      if ($reporte['status'] !== 'resuelto') {
        throw new Exception('El estado del reporte no es válido.');
      }

      // Actualizar
      Model::update('posts', ['id' => $id], ['status' => 'pendiente']);

      // Cargar de nuevo
      $reporte = Model::list('posts', ['id' => $id], 1); // cargar el post

      json_output(json_build(200, $reporte, sprintf('Reporte #%s marcado como pendiente con éxito.', $reporte['permalink'])));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function datatables()
  {
    try {
      $_GET['page']             = $this->data['page']; // es requerido para paginationhandler
      $registrosPorPagina       = $this->data['per_page']; // el parámetro que recibimos de javascript para cuántos registros por página
      $posts                    = PaginationHandler::paginate('SELECT * FROM pruebas ORDER BY id', [], $registrosPorPagina); // cargamos todo de la db
      $posts['recordsTotal']    = $posts['total']; // necesitamos esto para crear una paginación correcta
      $posts['recordsFiltered'] = $posts['total']; // necesitamos esto para crear una paginación correcta
      json_output(json_encode($posts));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function listar_clientes()
  {
    $sEcho                     = $this->data['sEcho'];
    $iDisplayStart             = $this->data['iDisplayStart']; // Registro para comenzar la paginación
    $iDisplayLength            = $this->data['iDisplayLength']; // Cantidad de registros por página

    // Cuenta el total de registros en la db
    $iTotalRecords             = Model::query('SELECT COUNT(id) AS total FROM clientes')[0]['total']; // Records totales en la db

    // Carga los registros paginados
    $sql                       = 'SELECT id, nombre, apellidos, colonia, mac FROM clientes ORDER BY id DESC LIMIT %s OFFSET %s';
    $rows                      = Model::query(sprintf($sql, $iDisplayLength, $iDisplayStart));

    // https://legacy.datatables.net/release-datatables/examples/data_sources/server_side.html
    $iTotalDisplayRecords      = 0; // Records encontrados para mostrar filtrados en caso de tener filtros

    // Procesar las columnas para que sean aceptadas correctamente en el frontend
    $aaData = array_map(function ($cliente) {
      return [
        0 => $cliente['id'],
        1 => $cliente['nombre'],
        2 => $cliente['apellidos'],
        3 => $cliente['colonia'],
        4 => $cliente['mac']
      ];
    }, $rows);

    $result = array(
      "sEcho"                => $sEcho,
      "iTotalRecords"        => $iTotalRecords,
      "iTotalDisplayRecords" => $iTotalRecords,
      "aaData"               => $aaData
    );

    json_output(json_encode($result)); // Sólo es para sacar con Content-Type application/json el contenido
  }

  function eventos()
  {
    try {
      $start   = isset($_GET["start"]) ? date('Y-m-d', strtotime($_GET["start"])) : null;
      $end     = isset($_GET["end"]) ? date('Y-m-d', strtotime($_GET["end"])) : null;

      // Aplicar filtros si existen
      if ($start !== null && $end !== null) {
        $sql     = 'SELECT * FROM posts WHERE tipo = "evento" AND DATE(creado) BETWEEN :inicio AND :fin ORDER BY id';
        $eventos = Model::query($sql, ['inicio' => $start, 'fin' => $end]);
      } else {
        $sql     = 'SELECT * FROM posts WHERE tipo = "evento" ORDER BY id';
        $eventos = Model::query($sql);
      }

      if (!empty($eventos)) {
        $eventos = array_map(function($evento) {
          return [
            'id'     => $evento['id'],
            'title'  => $evento['titulo'],
            'start'  => date('Y-m-d', strtotime($evento['creado'])),
            'color'  => $evento['status'],
            'allDay' => true
          ];
        }, $eventos);
      } else {
        $eventos = [];
      }

      json_output(json_encode($eventos));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function evento($id)
  {
    try {
      $evento = Model::list('posts', ['id' => $id, 'tipo' => 'evento'], 1);

      if (!$evento) {
        throw new Exception('No existe el evento en la base de datos.');
      }

      $evento['fecha'] = format_date($evento['creado']);

      json_output(json_build(200, $evento));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function agregar_evento()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['titulo','fecha','color'], $this->data)) {
        throw new Exception('Completa el formulario por favor.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $titulo   = $this->data['titulo'];
      $fecha    = $this->data['fecha'];
      $color    = $this->data['color'];

      $data     =
      [
        'tipo'       => 'evento',
        'titulo'     => $titulo,
        'status'     => $color,
        'creado'     => $fecha
      ];

      // Verificar si ya existe el post en la base de datos
      $id      = Model::add('posts', $data);
      $evento  = Model::list('posts', ['id' => $id], 1); // cargar el post

      json_output(json_build(201, $evento, 'Nuevo evento registrado con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function actualizar_evento()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['id','fecha'], $this->data)) {
        throw new Exception('Parámetros incompletos.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $id       = $this->data['id'];
      $fecha    = date('Y-m-d H:i:s', strtotime($this->data['fecha']));

      // Validar que exista el evento
      $evento = Model::list('posts', ['id' => $id, 'tipo' => 'evento'], 1);

      if (!$evento) {
        throw new Exception('No existe el evento en la base de datos.');
      }

      // Verificar si ya existe el post en la base de datos
      $res     = Model::update('posts', ['id' => $id], ['creado' => $fecha]);
      $evento  = Model::list('posts', ['id' => $id], 1); // cargar el post

      json_output(json_build(200, $evento, 'Evento actualizado con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function eliminar_evento()
  {
    try {
      if (!check_posted_data(['id'], $this->data)) {
        throw new Exception('Parámetros faltantes.');
      }
      $id     = $this->data['id'];
      $evento = Model::list('posts', ['id' => $id, 'tipo' => 'evento'], 1);

      if (!$evento) {
        throw new Exception('No existe el evento en la base de datos.');
      }

      // Borrar el evento
      Model::remove('posts', ['id' => $id], 1);

      json_output(json_build(200, $evento, 'Evento eliminado con éxito.'));

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////// GEORGE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////

  function agregar_documento()
  {
    try {
      // Validar que recibimos los parámetros necesarios
      if (!check_posted_data(['nombre','email','mensaje'], $this->data)) {
        throw new Exception('Completa el formulario por favor.');
      }

      // Inicializar el array de información a insertar o actualizar
      array_map('sanitize_input', $this->data);
      $nombre     = $this->data['nombre'];
      $email      = $this->data['email'];
      $mensaje    = $this->data['mensaje'];
      $nombre_pdf = generate_filename();
      $contenido  = sprintf('<h1>Mensaje del usuario</h1><p>%s</p>', $mensaje);

      // Generamos el pdf
      $pdf = new BeePdf();
      $pdf->create($nombre_pdf, $contenido, false, true);
      
      // Insertas el registro
      $data     =
      [
        'nombre'     => $nombre,
        'email'      => $email,
        'mensaje'    => $mensaje,
        'nombre_pdf' => $nombre_pdf . '.pdf',
        'creado'     => date('Y-m-d H:i:s')
      ];

      if (!$id = Model::add('documentos', $data)) {
        throw new Exception('Hubo un problema al generar tu PDF.');
      }

      // Regresarías
      // $sql = 'SELECT * FROM documentos WHERE id = :id LIMIT 1';
      $documento = Model::list('documentos', ['id' => $id], 1);
      $documento['url_pdf'] = UPLOADED . $documento['nombre_pdf'];

      json_output(json_build(201, $documento, 'Nuevo documento guardado con éxito.'));

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
  function cargar_reportes_sse()
  {
    try {
      // Cabeceras para SSE
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache');

      // Último ID enviado al cliente
      $lastSentId = 0;

      while (true) {
        // Obtener nuevos datos desde la db
        $sql     = 'SELECT * FROM posts WHERE tipo = "reporte" AND id > :id ORDER BY id ASC';
        $newData = Model::query($sql, ['id' => $lastSentId]);

        if (!$newData) {
          sleep(5); // Dormir por 5 segundos antes de enviar otra actualización
          continue; // Continuar al siguiente ciclo
        }

        // Actualizar el último ID enviado al cliente
        $lastSentId = max(array_column($newData, 'id')); // el último ID con el máximo valor del array de datos

        // Si hay nuevos registros en la base de datos, regresarlos
        echo 'data: ' . json_encode($newData) . "\n\n";
        ob_flush();
        flush();
      }

    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }
}