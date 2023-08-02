<?php 

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;

class View {

  /**
   * El path a la carpeta de vistas del controladores actual
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
    }
  }

  /**
   * Renderiza una vista con el motor de bee regular
   *
   * @param string $view
   * @param array $data
   * @return void
   */
  function renderBeeTemplate($view, $data = [])
  {
    // Validar si existe el folder del controlador
    if (!is_dir($this->viewsDir . $this->controller)) {
      die(sprintf('No existe la carpeta de vistas del controlador "%s".', $this->controller));
    }

    // Validar si existe la vista solicitada
    if (!is_file($this->viewsDir . $this->DS . $this->controller . $this->DS . $view . 'View.php')) {
      die(sprintf('No existe la vista "%sView" en la carpeta "%s".', $view, $this->controller));
    }

    // Convertir el array asociativo en objeto
    if (is_array($data) && !is_object($data)) {
      $d = to_object($data); // $data en array assoc o $d en objectos
    }

    require_once $this->viewsDir . $this->DS . $this->controller . $this->DS . $view . 'View.php';
  }

  /**
   * Renderiza una vista de twig
   *
   * @param string $view
   * @param array $data
   * @return void
   */
  function renderTwigTemplate($view, $data = [])
  {
    try {
      // Validar si existe el folder del controlador
      if (!is_dir($this->viewsDir . $this->controller)) {
        die(sprintf('No existe la carpeta de vistas del controlador "%s".', $this->controller));
      }
  
      // Validar si existe la vista solicitada
      $file = $view . "View.twig";
      if (!is_file($this->viewsDir . $this->controller . $this->DS . $file)) {
        die(sprintf('No existe la vista "%s" en la carpeta "%s".', $view, $this->controller));
      }

      // Carga de todos los filtros y funciones añadidas
      $this->getTwigFilters();
      $this->getTwigFunctions();

      echo $this->twigIntance->render(sprintf('%s%sView.twig', $this->path, $view), $data);

    } catch (LoaderError $e) {
      die("Hay un error del cargador: " . $e->getMessage());
    } catch (Error $e) {
      die("Hay un error fatal: " . $e->getMessage());
    } catch (SyntaxError $e) {
      die("Hay un error de sintaxis: " . $e->getMessage());
    }
  }

  /**
   * Carga los filtros registros para usar dentro de twig
   *
   * @return void
   */
  function getTwigFilters()
  {
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
    $this->twigIntance->addFunction(
      new TwigFunction('get_base_url', 'get_base_url')
    );

    $this->twigIntance->addFunction(
      new TwigFunction('money', 'money')
    );

    $this->twigIntance->addFunction(
      new TwigFunction('basepath', function() {
        return BASEPATH;
      })
    );
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
  public static function render($view, $data = [], $templateEngine = null)
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
  public static function render_twig($view, $data = [])
  {
    // Inicializar la instancia de nuestra clase
    $engine = new self('twig');
    $engine->renderTwigTemplate($view, $data);
  }
}