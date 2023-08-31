<?php 

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;

class View {

  /**
   * El path a la carpeta de vistas del controlador actual
   *
   * @var string
   */
  private $path           = null;

  /**
   * El directorio base para el cargador de recursos
   *
   * @var string
   */
  private $baseDir        = TEMPLATES;

  /**
   * El directorio base para el directorio de las vistas
   *
   * @var string
   */
  private $viewsDir       = VIEWS;

  /**
   * El controlador actual cargado
   *
   * @var string
   */
  private $controller     = CONTROLLER;

  /**
   * Separador de directorios \
   *
   * @var string
   */
  private $DS             = DS;

  /**
   * Cargador de recursos de twig
   *
   * @var FilesystemLoader
   */
  private $twigLoader     = null;

  /**
   * Instancia del moto twig
   *
   * @var Environment
   */
  private $twigIntance    = null;

  /**
   * El motor de plantillas a ser utilizado
   *
   * @var string
   */
  private $templateEngine = 'bee';

  /**
   * La vista a ser renderizada
   *
   * @var string
   */
  private $currentView    = null;
  
  function __construct($engine = null)
  {
    if ($engine !== null) {
      $this->templateEngine = $engine;
    }

    // Inicializar los servicios del motor de plantillas
    $this->setUpTwigTemplateEngine();
    
    // Definimos el path directo a la carpeta de vistas de la instancia de la clase
    $this->path = 'views' . $this->DS . $this->controller . $this->DS;
  }

  /**
   * Establece los valores por defecto del path de recursos y templates de twig
   *
   * @return void
   */
  function setUpTwigTemplateEngine()
  {
    if ((defined('USE_TWIG') && USE_TWIG === true) || $this->templateEngine == 'twig') {
      $this->templateEngine = 'twig';
      $this->twigLoader     = new FilesystemLoader($this->baseDir);
      $this->twigIntance    = new Environment($this->twigLoader);
      $this->registerFunctions();
    }
  }

  /**
   * Registra todas las funciones creadas por el usuario
   *
   * @return void
   */
  private function registerFunctions()
  {
    // Todas las funciones definidas y cargadas en Bee framework
    $functions = get_defined_functions();
    foreach ($functions['user'] as $function) {
      $twigFunction = new TwigFunction($function, $function);
      $this->twigIntance->addFunction($twigFunction);
    }
  }

  /**
   * Renderiza una vista con el motor de bee regular
   *
   * @param string $view
   * @param array $data
   * @return void
   */
  function renderBeeTemplate(string $view, array $data = [])
  {
    // Vista actual a renderizar
    $this->currentView = sprintf('%sView.php', $view);

    // Validar si existe el folder del controlador
    if (!is_dir($this->viewsDir . $this->controller)) {
      die(sprintf('No existe la carpeta de vistas del controlador "%s".', $this->controller));
    }

    // Validar si existe la vista solicitada
    if (!is_file($this->viewsDir . $this->DS . $this->controller . $this->DS . $this->currentView)) {
      die(sprintf('No existe la vista "%sView" en la carpeta "%s".', $view, $this->controller));
    }

    // Convertir el array asociativo en objeto
    if (is_array($data) && !is_object($data)) {
      $d = to_object($data); // $data en array assoc o $d en objectos
    }

    require_once $this->viewsDir . $this->DS . $this->controller . $this->DS . $this->currentView;
  }

  /**
   * Renderiza una vista de twig
   *
   * @param string $view
   * @param array $data
   * @return void
   */
  function renderTwigTemplate(string $view, array $data = [])
  {
    // TODO: Implementar que si es pasado un path completo a una vista, se busque dentro del directorio de vistas y no sólo en la carpeta del controlador
    try {
      // Vista actual a renderizar
      $this->currentView = sprintf('%sView.twig', $view);

      // Validar si existe el folder del controlador
      if (!is_dir($this->viewsDir . $this->controller)) {
        die(sprintf('No existe la carpeta de vistas del controlador "%s".', $this->controller));
      }
  
      // Validar si existe la vista solicitada
      if (!is_file($this->viewsDir . $this->controller . $this->DS . $this->currentView)) {
        die(sprintf('No existe la vista "%s" en la carpeta "%s".', $view, $this->controller));
      }

      // Carga de todos los filtros y funciones añadidas
      $this->getTwigFilters();
      $this->getTwigFunctions();

      echo $this->twigIntance->render(sprintf('%s%s', $this->path, $this->currentView), $data);

    } catch (LoaderError $e) {
      die("Hay un error del cargador: " . $e->getMessage());
    } catch (Error $e) {
      die("Hay un error fatal: " . $e->getMessage());
    } catch (SyntaxError $e) {
      die("Hay un error de sintaxis: " . $e->getMessage());
    } catch (LogicException $e) {
      die("Hay un error de lógica: " . $e->getMessage());
    }
  }

  private function viewExists()
  {
    // Validar si existe la vista pasada con el PATH completo
    // Validar si existe la vista dentro del folder de vistas
    // Validar si exista la vista dentro de su carpeta de controlador
    if (is_file($this->currentView)) {
      return $this->currentView;
    } else if (is_file($this->viewsDir . $this->currentView)) {
      return $this->viewsDir . $this->currentView;
    } else if (is_file($this->viewsDir . $this->controller . $this->DS . $this->currentView)) {
      return $this->viewsDir . $this->controller . $this->DS . $this->currentView;
    } else {
      return false;
    }
  }

  /**
   * Carga los filtros registros para usar dentro de twig
   *
   * @return void
   */
  function getTwigFilters()
  {
    BeeHookManager::runHook('on_get_twig_filters', $this->twigIntance); // Permite registrar nuevos filtros
    
    // Regresa el hash md5 del string pasado
    $this->twigIntance->addFilter(
      new TwigFilter('md5', function($arg) { 
        return md5($arg); 
      })
    );
  }

  /**
   * Carga las funciones registradas para usar dentro de twig
   *
   * @return void
   */
  function getTwigFunctions()
  {
    BeeHookManager::runHook('on_get_twig_functions', $this->twigIntance); // Permite registrar nuevas funciones

    // $this->twigIntance->addFunction(
    //   new TwigFunction('get_base_url', 'get_base_url')
    // );

    // $this->twigIntance->addFunction(
    //   new TwigFunction('money', 'money')
    // );

    // $this->twigIntance->addFunction(
    //   new TwigFunction('basepath', function() {
    //     return BASEPATH;
    //   })
    // );
  }

  /**
   * Renderiza una vista con el motor por defecto configurado o también
   * usando twig de forma explícita
   *
   * @param string $view
   * @param array $data
   * @param string $templateEngine
   * @return mixed
   */
  public static function render(string $view, array $data = [], ?string $templateEngine = null)
  {
    // Inicializar la instancia de nuestra clase
    $engine = new self($templateEngine);

    // Verificar que motor de templates usamos
    switch ($engine->templateEngine) {
      case 'twig':
        $engine->renderTwigTemplate($view, $data);
        break;
      
      case 'bee':
        $engine->renderBeeTemplate($view, $data);
        break;

      default:
        die("Motor de plantillas no válido");
        break;
    }
  }

  /**
   * Renderiza una vista usando el motor twig
   *
   * @param string $view
   * @param array $data
   * @return mixed
   */
  public static function render_twig(string $view, array $data = [])
  {
    // Inicializar la instancia de nuestra clase
    $engine = new self('twig');
    $engine->renderTwigTemplate($view, $data);
  }
}