<?php 

class Controller {
  protected ?String $controller = null;
  protected ?String $method     = null;
  protected ?String $viewName   = 'index';
  protected Array $data         = [];

  function __construct()
  {
    // Valores iniciales del controlador y del método ejecutado
    $this->controller = CONTROLLER;
    $this->method     = METHOD;

    // Definir el título por defecto de la página
    $this->addToData('title'      , 'Reemplaza el título de la página');
    $this->addToData('controller' , $this->controller);
    $this->addToData('method'     , $this->method);

    // Autenticación del usuario
    $this->addToData('auth'       , is_logged());
    $this->addToData('user'       , get_user());

    // Carrito de compras
    // TODO: Configurar si se usarán o no carritos de compras
    $this->addToData('cart'       , BeeCartHandler::get());
  }

  /**
   * Función para validar la sesión de un usuario, puede ser usada
   * en cualquier controlador hijo o que extienda el Controller
   *
   * @return void
   */
  function auth()
  {
    if (!Auth::validate()) {
      Flasher::new('Área protegida, debes iniciar sesión para visualizar el contenido.', 'danger');
      Redirect::back('login');
    }
  }

  function addToData(String $key, Mixed $value)
  {
    $this->data[$key] = $value;
  }

  function setData(Array $data)
  {
    $this->data = $data;
  }

  function getData()
  {
    return $this->data;
  }

  function setView(String $viewName)
  {
    $this->viewName = $viewName;
  }
  
  function render()
  {
    View::render($this->viewName, $this->data);
  }
}