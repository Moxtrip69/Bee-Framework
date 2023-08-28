<?php 

class Controller {
  protected string $controllerType = 'regular'; // regular | ajax | endpoint
  protected ?string $controller    = null;
  protected ?string $method        = null;
  protected ?string $viewName      = 'index';
  protected string $engine         = 'bee';
  protected ?array $data           = [];
  protected ?array $files          = [];

  protected $http                  = null;
  protected $request               = null;


  function __construct(string $controllerType = 'regular')
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

  /**
   * Ejecuta la funcionalidad dependiendo del tipo de controlador
   *
   * @return void
   */
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

  /**
   * Se ejecuta cuando es un controlador regular con funcionalidad genérica
   * y su información por defecto incluida en $data
   *
   * @return void
   */
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

  /**
   * Se ejecuta si es el controlador de AJAX
   *
   * @return void
   */
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

  /**
   * Se ejecuta si el controlador es un endpoint
   *
   * @return void
   */
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

  /**
   * Define el título de la página o ruta actual
   *
   * @param string $pageTitle
   * @return void
   */
  protected function setTitle(string $pageTitle)
  {
    $this->data['title'] = $pageTitle;
  }

  /**
   * Agrega un elemento a $data que será pasada a la vista
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  protected function addToData(string $key, $value)
  {
    $this->data[$key] = $value;
  }

  /**
   * Define todo el contenido de $data que será pasada a la vista
   *
   * @param array $data
   * @return void
   */
  protected function setData(array $data)
  {
    $this->data = $data;
  }

  /**
   * Regresa todo el contenido de $data
   *
   * @return array
   */
  protected function getData()
  {
    return $this->data;
  }

  /**
   * Define el engine de vistas a ser utilizado en la ruta actual
   *
   * @param string $engine
   * @return void
   */
  protected function setEngine(string $engine)
  {
    $this->engine = $engine;
  }

  /**
   * Define el nombre de la vista a ser utilizada en la ruta actual
   *
   * @param string $viewName
   * @return void
   */
  protected function setView(string $viewName)
  {
    $this->viewName = $viewName;
  }
  
  /**
   * Realiza el renderizado de la vista
   *
   * @return void
   */
  protected function render()
  {
    View::render($this->viewName, $this->data, $this->engine);
  }
}