<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de test
 */
class testController extends Controller implements ControllerInterface {
  function __construct()
  {
    // Prevenir el ingreso en Producción
    if (!is_local()) {
      Redirect::to(DEFAULT_CONTROLLER);
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }

  function index()
  {
    // Slider
    $slider = new Bs5Slider;
    $slider->setId('slider-1');
    $slider->setClasses('shadow-lg rounded-3');
    $slider->setInterval(2000);
    $slider->setControls(true);
    $slider->setTransition('slide');
    $slider->setAutoplay(false);
    $slider->setImages(
      [
      UPLOADED . 'slide01.jpg',
      UPLOADED . 'slide02.jpg',
      UPLOADED . 'slide03.jpg',
      ]
    );

    // Card
    $card = new Bs5Card;
    $card->setImage(UPLOADED . 'slide01.jpg');
    $card->setHeader('Una nueva carta');
    $card->addHeaderButton('Botón 1', '#', 'btn-primary');
    $card->addHeaderButton('Botón 2', '#', 'btn-secondary');
    $card->setTitle('El título');
    $card->setBody('Contenido de la tarjeta.<br><button class="btn btn-success">Ingresar</button>');
    $card->setFooter('Pie de la Tarjeta');

    // Accordion
    $accordion = new Bs5Accordion();
    $accordion->addItem('Item 1', 'Contenido del ítem 1.');
    $accordion->addItem('Item 2', 'Contenido del ítem 2.', false);
    $accordion->addItem('Item 3', 'Contenido del ítem 3.');

    $this->setTitle('Título de pruebas');
    $this->addToData('algo', 123);
    $this->addToData('slider', $slider->render());
    $this->addToData('card', $card->render());
    $this->addToData('accordion', $accordion->render());
    $this->setView('index');
    $this->render();
  }

  function roles()
  {
    // Pruebas de roles
    // Tienda de ropa
    // 2 roles
    // vendedor
    // almacenista

    $role = new BeeRoleManager();
    // $role->addRole('Vendedor de ropa', 'vendedor-ropa');
    // $role->addRole('Almacenista de ropa', 'almacenista');
    // debug($role->getRoles());

    // acomodar-ropa
    // vender
    // empacar
    // cobrar

    // $permiso = new BeeRoleManager();
    // $permiso->addPermission('Acomodar la ropa', 'acomodar-ropa', 'Puede acomodar toda la mercancía en los estantes.');
    // $permiso->addPermission('Vender', 'vender', 'Puede realizar ventas en mostrador.');
    // $permiso->addPermission('Empacar', 'empacar', 'Puede empacar las ventas para envío.');
    // $permiso->addPermission('Cobrar', 'cobrar', 'Puede cobrar dinero de los clientes.');

    // $role = new BeeRoleManager('almacenista');

    // $role->allow('empacar');
    // $role->allow('acomodar-ropa');

    // debug($role->getRole());

    $soy = 'almacenista';

    $role = new BeeRoleManager($soy);
    debug($role->getRole());

    if ($role->can('vender')) {
      echo 'Estas generando una nueva venta.';
    } else {
      echo 'No tienes permisos para vender.';
    }

    if ($role->can('acomodar-ropa')) {
      echo 'Puedes acomodar la ropa en los estantes.';
    } else {
      echo 'No puedes acomodar la ropa en los estantes.';
    }

    if ($role->can('empacar')) {
      echo 'Puedes empacar las ventas para envío.';
    } else {
      echo 'No puedes empacar las ventas para envío.';
    }


  }

  function funciones()
  {
    get_cur_page(); // Url actual
    get_base_url(); // url base
    get_basepath(); // el basepath
    _e('Algo'); // placeholder si el valor no existe o es vacío
    get_image('miimagen.png'); // carga una imagen
    get_uploaded_image('imagensubida.png'); // carga una imagen subida
  }

  function paginacion()
  {
    $sql = 'SELECT * FROM productos ORDER BY id';
    $pag = new PaginationHandler;
    $pag->setBaseQuery($sql);
    $pag->setRecordsPerPage(1);
    $pag->setGetVariable('p');
    $pag->setDirection('ASC');
    debug($pag->launch());

    debug(PaginationHandler::paginate($sql, [], 3));
  }

  function quickcharts()
  {
    $chart = new BeeQuickChart('bar');
    $chart->setSize(500, 300);
    $chart->setLabels(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo']);

    $dataset1 = new BeeQuickChartDataset();
    $dataset1->setLabel('Ventas');
    $dataset1->setData([10, 20, 30, 25, 12]);
    $dataset1->setBaseColor('#e84118', 0.75);
    $dataset1->setBorder(10, 5);
    $dataset1->setPointRadius(3);
    $chart->addDataset($dataset1);

    $dataset2 = new BeeQuickChartDataset();
    $dataset2->setLabel('Compras');
    $dataset2->setData([28, 22, 35, 49, 84]);
    $dataset2->setBaseColor('#273c75', 0.4);
    $chart->addDataset($dataset2);

    $dataset3 = new BeeQuickChartDataset();
    $dataset3->setLabel('Utilidades');
    $dataset3->setData([15, 18, 30, 38, 55]);
    $dataset3->setBaseColor('#273c75', 0.8);
    $chart->addDataset($dataset3);
    // $image = $chart->saveToImage();

    $this->addToData('title', 'QuickCharts');
    $this->addToData('url'  , $chart->getUrl());
    $this->setView('charts');
    $this->render();
  }

  function menus()
  {
    $link = new BeeMenuItemLink;
    $link->setIcon('<i class="fas fa-eye fa-fw me-1"></i>');
    $link->setText('Home');
    $link->setUrl(CUR_PAGE);
    $link->setClasses('nav-link');

    $item = new BeeMenuItem();
    $item->setSlug('home');
    $item->setLink($link);
    $item->setClasses('nav-item');

    $link2 = new BeeMenuItemLink;
    $link2->setIcon('<i class="fas fa-check fa-fw me-1"></i>');
    $link2->setText('Tienda');
    $link2->setUrl(CUR_PAGE);
    $link2->setClasses('nav-link');

    $item2 = new BeeMenuItem();
    $item2->setSlug('admin');
    $item2->setLink($link2);
    $item2->setClasses('nav-item');

    $link3 = new BeeMenuItemLink;
    $link3->setIcon('<i class="fas fa-heart fa-fw me-1"></i>');
    $link3->setText('Carrito');
    $link3->setUrl(CUR_PAGE);
    $link3->setClasses('nav-link');

    $item3 = new BeeMenuItem();
    $item3->setSlug('dashboard');
    $item3->setLink($link3);
    $item3->setClasses('nav-item');

    $link4 = new BeeMenuItemLink;
    $link4->setIcon('<i class="fas fa-list fa-fw me-1"></i>');
    $link4->setText('Listado');
    $link4->setUrl(CUR_PAGE);
    $link4->setClasses('nav-link');

    $item4 = new BeeMenuItem();
    $item4->setSlug('listado');
    $item4->setLink($link4);
    $item4->setClasses('nav-item');

    $link5 = new BeeMenuItemLink;
    $link5->setText('Ingresar');
    $link5->setUrl(CUR_PAGE);
    $link5->setClasses('btn btn-success btn-sm');

    $item5 = new BeeMenuItem();
    $item5->setSlug('listado');
    $item5->setLink($link5);
    $item5->setClasses('nav-item d-flex align-items-center');

    // Elementos del menú
    $menuLinks =
    [
      [
        'text'  => 'Home',
        'url'   => URL,
        'slug'  => 'home',
        'admin' => false
      ],
      [
        'text'  => 'Tienda',
        'url'   => URL,
        'slug'  => 'tienda',
        'admin' => false
      ],
      [
        'text'  => 'Carrito',
        'url'   => URL,
        'slug'  => 'carrito',
        'admin' => false
      ],
      [
        'text'  => 'Administración',
        'url'   => URL,
        'slug'  => 'admin',
        'admin' => true
      ],
    ];

    /// Crear el menú de forma dinámica
    $items = [];
    foreach ($menuLinks as $menuLink) {
      if ($menuLink['admin'] === true && !is_logged()) continue;

      // Visualizar el link en el menú si no es sólo para admins
      $link = new BeeMenuItemLink;
      // $link->setIcon('<i class="fas fa-eye fa-fw me-1"></i>');
      $link->setText($menuLink['text']);
      $link->setUrl($menuLink['url']);
      $link->setClasses('nav-link');

      $item = new BeeMenuItem();
      $item->setSlug($menuLink['slug']);
      $item->setLink($link);
      $item->setClasses('nav-item');

      $items[] = $item;
    }

    $menu = new BeeMenuBuilder('myNavbar', 'ul', 'navbar-nav', 'active');
    $menu->setCurrentSlug('tienda');
    $menu->addItems($items);

    $this->setTitle('Título de pruebas');
    $this->addToData('menu', $menu->getMenu());
    $this->setView('menus');
    $this->render();
  }

  function three()
  {
    $data =
    [
      'title' => 'Threejs'
    ];

    View::render('three', $data);
  }

  function forms()
  {
    // Nuevo formulario
    $form = new BeeFormBuilder('test-form', 'test-form', ['una-clase'], 'test/post_test', true, true);

    // Agregar inputs personalizados (puede servir para intectar el token csrf al formulario)
    $form->addCustomFields(insert_inputs());

    // Ocultos
    $form->addHiddenField('id', 'El ID del usuario', ['form-control'], 'id', true, 123);

    // Nombre y apellidos
    $form->addCustomFields('<div class="row">');
    $form->addCustomFields('<div class="col-6">');
    $form->addTextField('nombre', 'Tu nombre', ['form-control'], 'nombre', true, 'Pancho');
    $form->addCustomFields('</div>');
    $form->addCustomFields('<div class="col-6">');
    $form->addTextField('apellido', 'Tu apellido', ['form-control'], 'apellido', true, 'Villa');
    $form->addCustomFields('</div>');
    $form->addCustomFields('</div>');

    // Correo electrónico
    $form->addEmailField('email', 'Correo electrónico', ['form-control'], 'email', true, 'pancho@doe.com');

    // Contraseña
    $form->addPasswordField('contraseña', 'Tu contraseña', ['form-control'], 'password', true);

    // Seleccionable
    $options = [
      'option1' => 'Opción 1',
      'option2' => 'Opción 2',
      'option3' => 'Opción 3',
      'option4' => 'Opción 4'
    ];
    $form->addSelectField('país', 'Tu país', $options, ['form-select'], 'pais', true, 'option2');

    // Radio y checkbox
    $form->addRadioField('aceptar', 'Aceptas los términos y condiciones', 'si', ['form-check-input'], 'aceptar', false, true);
    $form->addCheckboxField('recordar', 'Recordar mis datos', 'si', ['form-check-input'], 'recordar', false, true);

    // Textarea
    $form->addTextareaField('contenido', 'Contenido de la entrada', 10, 5, ['form-control'], 'contenido', true, 'Lorem ipsum dolor sit amet.');

    // Archivos
    $form->addFileField('imagen', 'Tu imagen de perfil', ['form-control'], 'imagen', true);
    $form->addFileField('avatar', 'Tu avatar', ['form-control'], 'avatar');

    // Sliders
    $form->addSliderField('valoración', 'Tu calificación', 1, 5, 1, ['form-range'], 'valoracion', true);

    // Número
    $form->addNumberField('edad', 'Tu edad', 15, 99, 1, 18, ['form-control'], 'edad', true);

    // Color
    $form->addColorField('color', 'Tu color favorito', ['form-control form-control-color'], 'color', false, 'fff');
    
    // Fechas
    $form->addDateField('fecha', 'La fecha', date('Y-m-d'), ['form-control'], 'fecha', true);
    
    // Agregando botones
    $form->addButton('submit', 'submit', 'Enviar formulario', ['btn btn-success me-2'], 'submit-button');

    $html   = $form->getFormHtml(); // El formulario en sí
    $script = $form->generateFetchScript(URL . 'api/form-builder', API_PRIVATE_KEY); // Script generado automaticamente para enviar los datos con AJAX

    $this->setTitle('FormBuilder');
    $this->addToData('form', $html);
    $this->addToData('script', $script);
    $this->setView('forms');
    $this->render();
  }

  function db_user()
  {
    // Ejemplo de uso de nuestra clase Db en POO evitando el uso de métodos estáticos
    try {
      $sql   = 'SELECT * FROM pruebas';
      $db    = new Db();
      $conn  = $db->link();

      // Inicializar la transacción
      $conn->beginTransaction();

      // Enunciados SQL
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('John')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Juan')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rigoberto')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rolon')");

      // Aplicar los cambios
      $conn->commit();
      echo "Nuevos registros agregados con éxito.";

    } catch (PDOException $e) {
      // Si hubo algún fallo, hacer rollback
      $conn->rollback();
      echo "Hubo un error: " . $e->getMessage();

    }
  }

  function create_table()
  {
    try {
      // Si es requerido podemos hacer un drop table if exists
      // Model::drop($table_name); // Para borrar una tabla de la base de datos
      $table_name = 'reportes';

      // Creamos un TableSchema
      $table      = new TableSchema($table_name);

      // Columnas de la tabla
      $table->add_column('id', 'int', 5, false, false, true, true);
      $table->add_column('nombre', 'varchar');
      $table->add_column('email', 'varchar');
      debug($table->get_sql());

      // Crea una tabla con base al TableSchema
      $res = Model::createTable($table);
      debug($res);
    } catch (PDOException $e) {
      echo 'Error' . $e->getMessage();
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  /**
   * @since 1.1.3
   * 
   * Genera un PDF de forma sencilla y dinámica
   *
   * @return void
   */
  function pdf()
  {
    try {
      $content = '<!DOCTYPE html>
      <html>
      <head>
      <style>
      code {
        font-family: Consolas,"courier new";
        color: crimson;
        background-color: #f1f1f1;
        padding: 2px;
        font-size: 80%%;
        border-radius: 5px;
      }
      </style>
      </head>
      <body>
  
      <img src="%s" alt="%s" style="width: 100px;"><br>
  
      <h1>Bienvenido de nuevo a %s</h1>
      <p>Versión <b>%s</b></p>
      
      <code>
      // Método 1
      $content = "Contenido del documento PDF, puedes usar cualquier tipo de HTML e incluso la mayoría de estilos CSS3";
      $pdf     = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true
  
      // Método 2
      $pdf = new BeePdf();
      $pdf->create("bee_pdfs", $content);
      </code>
  
      </body>
      </html>';
      $content = sprintf($content, get_bee_logo(), get_bee_name(), get_bee_name(), get_bee_version());

      // Método 1
      // $pdf = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true

      // Método 2
      $pdf = new BeePdf();
      $pdf->setOrientation('landscape'); // Nuevos método para establecer la orientación
      $pdf->setSize('A6'); // Nuevo método para establecer el tamaño
      $pdf->create('bee_pdfs', $content);

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('home');
    }
  }

  /**
   * Prueba para enviar correos electrónicos regulares
   *
   * @return void
   */
  function email()
  {
    try {
      if (!is_local()) {
        throw new Exception(get_bee_message(0));
      }

      $email   = 'jslocal@localhost.com';
      $subject = 'El asunto del correo';
      $body    = 'El cuerpo del mensaje, puede ser html o texto plano.';
      $alt     = 'El texto corto del correo, preview del contenido.';
      send_email(get_siteemail(), $email, $subject, $body, $alt);
      echo sprintf('Correo electrónico enviado con éxito a %s', $email);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  /**
   * @since 1.5.0
   * 
   * Prueba de envío de correos electrónicos usando SMTP
   *
   * @return void
   */
  function smtp()
  {
    try {
      if (!is_local()) {
        throw new Exception(get_bee_message(0));
      }

      send_email('tuemail@hotmail.com', 'tuemail@hotmail.com', 'Probando smtp', '¡Hola mundo!', 'Correo de prueba.');
      echo 'Mensaje enviado con éxito.';
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}