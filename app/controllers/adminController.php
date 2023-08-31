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
    $form->addTextField('nombre', 'Nombre del producto', ['form-control'], 'product-name', true);
    $form->addTextField('sku', 'SKU o número de rastreo', ['form-control'], 'product-sku');
    $form->addTextareaField('descripcion', 'Descripción del producto', 4, 5, ['form-control'], 'product-description');
    $form->addNumberField('precio', 'Precio principal', 1, 999999999, 'any', null, ['form-control'], 'product-price', true);
    $form->addNumberField('precio_comparacion', 'Precio de comparación', 1, 999999999, 'any', null, ['form-control'], 'product-compare-price');

    $form->addFileField('imagen', 'Imagen principal del producto', ['form-control'], 'product-imagen', true);

    $form->addCustomFields('<hr>');

    $form->addCheckboxField('rastrear_stock', 'Seguimiento de stock', 'true', ['form-check-input'], 'trackStock', false);
    $form->addNumberField('stock', 'Unidades disponibles', 1, 999999999, 1, null, ['form-control'], 'stock', false);

    $form->addButton('submit', 'submit', 'Agregar producto', ['btn btn-success'], 'submit-button');

    $this->setTitle('Productos');
    $this->addToData('form'     , $form->getFormHtml());
    $this->addToData('productos', productoModel::all_paginated());
    $this->addToData('slug'     , 'productos');
    $this->setView('productos/productos');
    $this->render();
  }

  function post_productos()
  {
    try {
      if (!check_posted_data(['nombre','sku','descripcion','precio','precio_comparacion','stock'], $_POST)) {
        throw new Exception('Por favor completa el formulario.');
      }

      if (!Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_bee_message(0));
      }

      // Definición de variables
      array_map('sanitize_input', $_POST);
      $nombre             = $_POST['nombre'];
      $sku                = $_POST["sku"];
      $descripcion        = $_POST["descripcion"];
      $precio             = (float) $_POST["precio"];
      $precio_comparacion = (float) $_POST["precio_comparacion"];
      $rastrear_stock     = isset($_POST["rastrear_stock"]) ? 1 : 0;
      $stock              = (int) $_POST["stock"];
      $imagen             = $_FILES["imagen"];
      $errorMessage       = '';
      $errors             = 0;

      // Crear slug con base al nombre del producto
      $slugify = new Slugify();
      $slug    = $slugify->slugify($nombre);

      // Verificar que no exista ya un producto con el sku si es que no está vacío
      $sql = 'SELECT * FROM productos WHERE sku = :sku OR nombre = :nombre OR slug = :slug';
      if (productoModel::query($sql, ['sku' => $sku, 'nombre' => $nombre, 'slug' => $slug])) {
        throw new Exception('Ya existe un producto registrado con el mismo SKU o nombre.');
      }

      // Validar longitud del nombre, no mayor a 150 caracteres
      if (strlen($nombre) > 150) {
        $errorMessage .= '- El nombre del producto debe ser menor a 150 caracteres.' . PHP_EOL;
        $errors++;
      }

      // Validar el precio regular del producto
      if ($precio == 0) {
        $errorMessage .= '- Ingresa un precio mayor a 0.' . PHP_EOL;
        $errors++;
      }

      // Validar el precio de comparación si no es igual a 0
      if ($precio_comparacion != 0 && $precio_comparacion < $precio) {
        $errorMessage .= '- El precio de comparación debe ser mayor al precio principal del producto.' . PHP_EOL;
        $errors++;
      }

      // Validación de la imagen
      if ($imagen['error'] !== 0) {
        $errorMessage .= '- Selecciona una imagen de producto válida por favor.' . PHP_EOL;
        $errors++;
      }

      // Procesar imagen
      $tmp_name = $imagen['tmp_name'];
      $filename = $imagen['name'];
      $type     = $imagen['type'];
      $ext      = pathinfo($filename, PATHINFO_EXTENSION);
      $new_name = generate_filename() . '.' . $ext;

      if (!move_uploaded_file($tmp_name, UPLOADS . $new_name)) {
        $errorMessage .= '- Hubo un problema al subir el archivo de imagen.' . PHP_EOL;
        $errors++;
      }

      if ($errors > 0) {
        if (is_file(UPLOADS . $new_name)) {
          unlink(UPLOADS . $new_name);
        }
        throw new Exception($errorMessage);
      }

      // Array de información del producto
      $data =
      [
        'nombre'             => $nombre,
        'slug'               => $slug,
        'sku'                => empty($sku) ? random_password(8, 'numeric') : $sku,
        'descripcion'        => $descripcion,
        'precio'             => $precio,
        'precio_comparacion' => $precio_comparacion,
        'rastrear_stock'     => $rastrear_stock,
        'stock'              => empty($stock) ? 0 : $stock,
        'imagen'             => $new_name,
        'creado'             => now()
      ];

      // Agregar producto a la base de datos
      if (!$id = productoModel::insertOne($data)) {
        throw new Exception('Hubo un error, intenta de nuevo.');
      }

      $producto = productoModel::by_id($id);

      Flasher::success(sprintf('Nuevo producto <b>%s</b> agregado con éxito.', $producto['nombre']));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}