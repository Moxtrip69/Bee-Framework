<?php 

class Controller {
  protected String $controllerType = 'regular'; // regular | ajax | endpoint
  protected ?String $controller    = null;
  protected ?String $method        = null;
  protected ?String $viewName      = 'index';
  protected String $engine         = 'bee';
  protected ?Array $data           = [];
  protected ?Array $files          = [];

  protected $http                  = null;
  protected $request               = null;


  function __construct(String $controllerType = 'regular')
  {
    $this->controllerType = $controllerType;
    $this->handleControllerType();
  }

  /**
   * Función para validar la sesión de un usuario, puede ser usada
   * en cualquier controlador hijo o que extienda el Controller
   *
   * @return void
   */
  protected function auth()
  {
    if (!Auth::validate()) {
      Flasher::new('Área protegida, debes iniciar sesión para visualizar el contenido.', 'danger');
      Redirect::back('login');
    }
  }

  private function handleControllerType()
  {
    switch ($this->controllerType) {
      case 'ajax':
        $this->isAjaxController();
        break;

      case 'endpoint':
        $this->isEndpointController();
        break;
        
      case 'regular':
      default:
        $this->isRegularController();
        break;
    }
  }

  private function isRegularController()
  {
    // Valores iniciales del controlador y del método ejecutado
    $this->controller = CONTROLLER;
    $this->method     = METHOD;

    // Validar el engine a utilizar por defecto
    $this->engine     = USE_TWIG === true ? 'twig' : $this->engine;

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

  private function isAjaxController()
  {
    // Prevenir el acceso no autorizado
    if (!defined('DOING_AJAX')) {
      http_response_code(403);
      json_output(json_build(403));
    }

    // Procesamos la petición que está siendo mandada al servidor
    try {
      $this->http    = new BeeHttp();
      $this->http->setCallType($this->controllerType);
      $this->http->process();
      $this->request = $this->http->get_request();
      $this->data    = $this->request['data'];
      $this->files   = $this->request['files'];
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));

    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, [], $e->getMessage()));

    }
  }

  private function isEndpointController()
  {
    // Prevenir el acceso no autorizado
    if (!defined('DOING_API')) {
      http_response_code(403);
      json_output(json_build(403));
    }

    // Procesamos la petición que está siendo mandada al servidor
    try {
      $this->http    = new BeeHttp();
      $this->http->setCallType($this->controllerType);
      $this->http->registerDomain('*'); // Recomiendo cambiar a un dominio específico por seguridad
      $this->http->process();
      $this->request = $this->http->get_request();
      $this->data    = $this->request['data'];
      $this->files   = $this->request['files'];
    } catch (BeeHttpException $e) {
      http_response_code($e->getStatusCode());
      json_output(json_build($e->getStatusCode(), [], $e->getMessage()));

    } catch (Exception $e) {
      http_response_code(400);
      json_output(json_build(400, [], $e->getMessage()));

    }
  }

  protected function setTitle(String $pageTitle)
  {
    $this->data['title'] = $pageTitle;
  }

  protected function addToData(String $key, Mixed $value)
  {
    $this->data[$key] = $value;
  }

  protected function setData(Array $data)
  {
    $this->data = $data;
  }

  protected function getData()
  {
    return $this->data;
  }

  protected function setEngine(String $engine)
  {
    $this->engine = $engine;
  }

  protected function setView(String $viewName)
  {
    $this->viewName = $viewName;
  }
  
  protected function render()
  {
    View::render($this->viewName, $this->data, $this->engine);
  }
}