<?php

use Cocur\Slugify\Slugify;

/**
 * Plantilla general de controladores
 * @version 1.0.5
 *
 * Controlador de admin
 */
class adminController extends Controller implements ControllerInterface {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index()
  {
    register_scripts([JS . 'admin/demo.js'], 'Chartjs gráficas para administración');

    $this->setTitle('Administración');
    $buttons =
    [
      [
        'url'   => 'admin',
        'class' => 'btn-danger text-white',
        'id'    => '',
        'icon'  => 'fas fa-download',
        'text'  => 'Descargar'
      ],
      [
        'url'   => 'admin',
        'class' => 'btn-success text-white',
        'id'    => '',
        'icon'  => 'fas fa-file-pdf',
        'text'  => 'Exportar'
      ]
    ];
    $this->addToData('buttons', $buttons);
    $this->render();
  }

  function perfil()
  {
    $this->setTitle('Perfil de usuario');
    $this->setView('perfil');
    $this->render();
  }

  function botones()
  {
    $this->setTitle('Botones');
    $this->setView('botones');
    $this->render();
  }

  function cartas()
  {
    $this->setTitle('Cartas');
    $this->setView('cartas');
    $this->render();
  }

  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //////// USUARIOS
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  function usuarios()
  {
    $this->setTitle('Usuarios');
    $this->addToData('users', userModel::all_paginated());
    $this->addToData('slug' , 'usuarios');
    $this->setView('usuarios/usuarios');
    $this->render();
  }

  function post_usuarios()
  {
    try {
      if (!check_posted_data(['username','email','password'], $_POST)) {
        throw new Exception('Por favor completa el formulario.');
      }

      if (!Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_bee_message(0));
      }

      // Definición de variables
      array_map('sanitize_input', $_POST);
      $username     = $_POST['username'];
      $email        = $_POST['email'];
      $password     = $_POST['password'];
      $errorMessage = '';
      $errors       = 0;

      // Verificar que no exista ya un usuario con ese username o correo electrónico
      $sql = 'SELECT * FROM bee_users WHERE username = :username OR email = :email';
      if (userModel::query($sql, ['username' => $username, 'email' => $email])) {
        throw new Exception('Ya existe un usuario registrado con ese nombre de usuario o correo electrónico.');
      }

      // Validaciones necesarias
      if (!preg_match('/^[a-zA-Z0-9]{5,20}$/', $username)) {
        $errorMessage .= '- Tu nombre de usuario debe estar formado por mínimo 5 caracteres y máximo 20.<br>';
        $errors++;
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage .= '- El correo electrónico no es válido.<br>';
        $errors++;
      }

      if (is_temporary_email($email)) {
        $errorMessage .= '- El dominio del correo electrónico no está autorizado.<br>';
        $errors++;
      }

      if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_-])[A-Za-z\d!@#$%^&*_-]{5,20}$/', $password)) {
        $errorMessage .= '- La contraseña debe ser de entre 5 y 20 caracteres, por lo menos debe contar con: 1 letra minúscula, 1 letra mayúscula, 1 digito y 1 caracter especial de entre <b>!@#$%^&*_-</b>';
        $errors++;
      }

      if ($errors > 0) {
        throw new Exception($errorMessage);
      }

      // Agregar el nuevo usuario a la base de datos
      $user     =
      [
        'username'   => $username,
        'email'      => $email,
        'password'   => password_hash($password . AUTH_SALT, PASSWORD_BCRYPT),
        'created_at' => now()
      ];

      // Insertando el registro en la base de datos
      if (!$id = userModel::add(userModel::$t1, $user)) {
        throw new Exception('Hubo un problema al agregar el usuario.');
      }

      Flasher::success(sprintf('Nuevo usuario agregado con éxito:<br>Usuario: <b>%s</b><br>Contraseña: <b>%s</b>', $user['username'], $password));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }

  function borrar_usuario($id = null)
  {
    try {
      if (!Csrf::validate($_GET['_t'])) {
        throw new Exception(get_bee_message(0));
      }

      // Verificar que exista el usuario
      if (!$user = userModel::by_id($id)) {
        throw new Exception('No existe el usuario en la base de datos.');
      }

      // Validar que no sea el propio usuario que está solicitando la petición
      if ($id == get_user('id')) {
        throw new Exception('No puedes realizar esta acción sobre ti mismo.');
      }

      // Borrando el registro de la base de datos
      if (!userModel::remove(userModel::$t1, ['id' => $id], 1)) {
        throw new Exception('Hubo un problema al borrar el usuario.');
      }

      Flasher::success(sprintf('Usuario <b>%s</b> borrado con éxito.', $user['username']));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }

  function destruir_sesion($id = null)
  {
    try {
      if (!Csrf::validate($_GET['_t'])) {
        throw new Exception(get_bee_message(0));
      }

      // Verificar que exista el usuario
      if (!$user = userModel::by_id($id)) {
        throw new Exception('No existe el usuario en la base de datos.');
      }

      // Validar que no sea el propio usuario que está solicitando la petición
      if ($id == get_user('id')) {
        throw new Exception('No puedes realizar esta acción sobre ti mismo.');
      }

      // Verificar que el usuario tenga una sesión activa
      if (empty($user['auth_token']) || $user['auth_token'] == null) {
        throw new Exception('El usuario no tiene una sesión activa.');
      }

      // Cerrando su sesión
      if (!userModel::update(userModel::$t1, ['id' => $id], ['auth_token' => null])) {
        throw new Exception('Hubo un problema al actualizar el usuario.');
      }

      Flasher::success(sprintf('La sesión de <b>%s</b> ha sido cerrada con éxito.', $user['username']));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }

  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //////// PRODUCTOS
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  function productos()
  {
    // Formulario para agregar nuevo registro
    $form = new BeeFormBuilder('agregar-producto', 'agregar-producto', ['needs-validation'], 'admin/post_productos', true, true);
    
    // Inputs
    $form->addCustomFields(insert_inputs());
    $form->addTextField('name', 'Nombre del producto', ['form-control'], 'product-name', true);
    $form->addTextField('sku', 'SKU o número de rastreo', ['form-control'], 'product-sku');
    $form->addTextareaField('description', 'Descripción del producto', 4, 5, ['form-control'], 'product-description', true);
    $form->addNumberField('price', 'Precio principal', 1, 999999999, 'any', null, ['form-control'], 'product-price', true);
    $form->addNumberField('compare_price', 'Precio de comparación', 1, 999999999, 'any', null, ['form-control'], 'product-compare-price');

    $form->addFileField('image', 'Imagen principal del producto', ['form-control'], 'product-imagen', true);

    $form->addCustomFields('<hr>');

    $form->addCheckboxField('trackStock', 'Seguimiento de stock', 'true', ['form-check-input'], 'trackStock', false);
    $form->addNumberField('stock', 'Unidades disponibles', 1, 999999999, 1, null, ['form-control'], 'stock', false);

    $form->addButton('submit', 'submit', 'Agregar producto', ['btn btn-success'], 'submit-button');

    $this->setTitle('Productos');
    $this->addToData('form'    , $form->getFormHtml());
    $this->addToData('products', productModel::all_paginated());
    $this->addToData('slug'    , 'productos');
    $this->setView('productos/productos');
    $this->render();
  }

  function post_productos()
  {
    try {
      if (!check_posted_data(['name','sku','description','price','compare_price','image','stock'], $_POST)) {
        throw new Exception('Por favor completa el formulario.');
      }

      if (!Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_bee_message(0));
      }

      // Definición de variables
      array_map('sanitize_input', $_POST);
      $name          = $_POST['name'];
      $sku           = $_POST["sku"];
      $description   = $_POST["description"];
      $price         = (float) $_POST["price"];
      $compare_price = (float) $_POST["compare_price"];
      $trackStock    = isset($_POST["trackStock"]) ? 1 : 0;
      $stock         = (int) $_POST["stock"];
      $image         = $_FILES["image"];
      $errorMessage  = '';
      $errors        = 0;

      // Crear slug con base al nombre del producto
      $slugify = new Slugify();
      $slug    = $slugify->slugify($name);

      // Verificar que no exista ya un producto con el sku si es que no está vacío
      $sql = 'SELECT * FROM products WHERE sku = :sku OR `name` = `:name` OR slug = :slug';
      if (productModel::query($sql, ['sku' => $sku, 'name' => $name, 'slug' => $slug])) {
        throw new Exception('Ya existe un producto registrado con el mismo SKU o nombre.');
      }

      // TODO: Validar que no exista uno con el mismo sku
      // TODO: Validar que no exista uno con el mismo nombre



      if ($errors > 0) {
        throw new Exception($errorMessage);
      }

      Flasher::success('Nuevo producto agregado con éxito.');
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}