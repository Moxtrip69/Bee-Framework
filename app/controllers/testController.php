<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de test
 */
class testController extends Controller {
  function __construct()
  {
    // Prevenir el ingreso en Producción
    if (!is_local()) {
      Redirect::to(DEFAULT_CONTROLLER);
    }
  }

  function index()
  {
    // session_destroy();
    $item1 = new BeeCartItem(1, 'Pack de desarrollo web Full Stack', 1000, 1, '', '', true);
    $item2 = new BeeCartItem(2, 'Emprendepack', 50);
    $item3 = new BeeCartItem(3, 'Súper pack de diseños', 200);
    $item4 = new BeeCartItem(4, 'Mascarilla de avena', 10);

    $customer = new BeeCartCustomer('Roberto', 'Orozco', 'jslocal@localhost.com', '5522334455');
    $customer->setLine1('Oriente 345 #123');
    $customer->setCity('Merced Balbuena');
    $customer->setState('CDMX');
    $customer->setCountry('México');
    $customer->setZp('15810');

    $shipping = new BeeCartShipping('Fedex', 125, strtotime('+3 days'));

    $coupon = new BeeCartCoupon('APOYOEDU2023', 'Apoyo educativo de la Academia de Joystick.', 70, 'percentage', strtotime('+10 days'));

    $cart  = new BeeCartHandler();
    // $cart->addItem($item1);
    // $cart->addItem($item2);
    // $cart->addItem($item3);
    // $cart->addItem($item4);
    $cart->addCustomer($customer);

    $cart->addShipping($shipping);

    $cart->addCoupon($coupon);

    // $cart->removeItem(2);

    // $cart->restartCart();
    debug($cart->loadCart());
  }

  function menus()
  {
    $item = new BeeMenuItem();
    $item->setSlug('inicio');
    $item->setText('Un enlace cool');
    $item->setUrl(URL);
    $item->setIcon('<i class="fas fa-fw fa-cog"></i>');

    $item2 = new BeeMenuItem();
    $item2->setSlug('admin');
    $item2->setText('Otro enlace nice');
    $item2->setUrl(URL);
    $item2->setIcon('<i class="fas fa-fw fa-eye"></i>');

    $item3 = new BeeMenuItem();
    $item3->setSlug('dashboard');
    $item3->setText('Tercer enlace');
    $item3->setUrl(URL);
    $item3->setIcon('<i class="fas fa-fw fa-eye"></i>');

    $menu = new BeeMenuBuilder();
    $menu->setCurrentSlug('admin');
    $menu->addItem($item);
    $menu->addItem($item2);
    $menu->addItem($item3);
    debug($menu->getMenu());
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
    $form->addTextField('nombre', 'Tu nombre', ['form-control'], 'nombre', true, 'Pancho');
    $form->addTextField('apellido', 'Tu apellido', ['form-control'], 'apellido', true, 'Villa');

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

    // Agregando botones
    $form->addButton('submit', 'submit', 'Enviar formulario', ['btn btn-success me-2'], 'submit-button');

    // Fechas
    $form->addDateField('fecha', 'La fecha', date('Y-m-d'), ['form-control'], 'fecha', true);

    $html   = $form->getFormHtml(); // El formulario en sí
    $script = $form->generateFetchScript(URL . 'api/form-builder', API_PRIVATE_KEY); // Script generado automaticamente para enviar los datos con AJAX

    $data   =
    [
      'title'  => 'Vista de prueba',
      'form'   => $html,
      'script' => $script
    ];

    View::render('index', $data);
  }
  
  function db_user()
  {
    try {
      $sql   = 'SELECT * FROM pruebas';
      $db    = new Db();
      $conn  = $db->link();
    
      // begin the transaction
      $conn->beginTransaction();
  
      // our SQL statements
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('John')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Juan')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rigoberto')");
      $conn->exec("INSERT INTO pruebas (nombre) VALUES ('Rolon')");
    
      // commit the transaction
      $conn->commit();
      echo "New records created successfully";
    } catch(PDOException $e) {
      // roll back the transaction if something failed
      $conn->rollback();
      echo "Error: " . $e->getMessage();
    }
  }
  
  function create_table()
  {
    try {
      // Si es requerido podemos hacer un drop table if exists
      // Model::drop($table_name); // Para borrar una tabla de la base de datos
      $table_name = 'usuarios';

      // Creamos un TableSchema
      $table      = new TableSchema($table_name);

      // Columnas de la tabla
      $table->add_column('id', 'int', 5, false, false, true, true);
      $table->add_column('nombre', 'varchar');
      $table->add_column('email', 'varchar');
      debug($table->get_sql());
      
      // Crea una tabla con base al TableSchema
      $res = Model::create($table);
      debug($res);

    } catch (PDOException $e) {
      echo $e->getMessage();
    } catch (Exception $e) {
      echo 'Regular: '.$e->getMessage();
    }
  }
}