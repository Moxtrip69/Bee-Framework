<?php 

/**
 * Controlador para generar modelos y controladores de forma dinámica
 */
class creatorController extends Controller implements ControllerInterface {
  function __construct()
  {
    // Prevenir el ingreso en Producción
    if (!is_local()) {
      Flasher::error(get_bee_message(0));
      Redirect::to(DEFAULT_CONTROLLER);
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index() {
    $files       = glob(CONTROLLERS . '*Controller.php');
    $controllers = [];

    foreach ($files as $f) {
      $basename = basename($f);
      $basename = str_replace('Controller.php', '', $basename);
      $controllers[] = $basename;
    }

    $this->setTitle('Crea un nuevo archivo');
    $this->addToData('controllers', $controllers);
    $this->setView('index');
    $this->render();
  }

  function post_controller()
  {
    if (!Csrf::validate($_POST['csrf'])) {
      Flasher::deny();
      Redirect::back();
    }

    // Validar nombre de archivo
    $name     = clean($_POST['filename']);
    $filename = strtolower($name);
    $filename = str_replace(' ', '_', $filename);
    $filename = str_replace('.php', '', $filename);
    $keyword  = 'Controller';
    $g_vista  = isset($_POST["generar-vista"]) ? true : false;
    $twig     = isset($_POST["usar-twig"]) ? true : false;
    $template = MODULES . 'bee' . DS . 'controllerTemplate.txt';

    // Validar que sea un string válido
    if (!is_string($name)) {
      Flasher::error(sprintf('Ingresa un nombre de controlador válido por favor.', $name));
      Redirect::back();
    }

    // Validar longitud del nombre
    if (strlen($name) < 5) {
      Flasher::error(sprintf('Ingresa un nombre de controlador válido por favor, <b>%s</b> es demasiado corto.', $name));
      Redirect::back();
    }

    // Validar la existencia del controlador para prevenir remover un archivo existente
    if (is_file(CONTROLLERS . $filename . $keyword . '.php')) {
      Flasher::new(sprintf('Ya existe el controlador %s.', $filename . $keyword), 'danger');
      Redirect::back();
    }

    // Validar la existencia de la plantilla.txt para crear el controlador
    if (!is_file($template)) {
      Flasher::new(sprintf('No existe la plantilla %s.', $template), 'danger');
      Redirect::back();
    }

    // Cargar contenido del archivo
    $php = @file_get_contents($template);
    $php = str_replace('[[REPLACE]]', $filename, $php);
    $php = str_replace('[[ENGINE]]', $twig ? 'twig' : 'bee', $php);

    // Generar el archivo del controlador
    if (file_put_contents(CONTROLLERS . $filename . $keyword . '.php', $php) === false) {
      Flasher::new(sprintf('Ocurrió un problema al crear el controlador %s.', $template), 'danger');
      Redirect::back();
    }

    // Crear el folder en carpeta vistas solo si es requerido
    if (!is_dir(VIEWS . $filename) && $g_vista === true) {
      mkdir(VIEWS . $filename);
    }

    // Generar la vista solo si así se solicita
    if ($g_vista === true) {
      $viewTemplate = MODULES . 'bee' . DS . ($twig ? 'viewTwigTemplate.txt' : 'viewTemplate.txt');

      if (!is_file($viewTemplate)) {
        Flasher::error(sprintf('La vista no fue creada, no existe la plantilla <b>%s</b>.', $viewTemplate));
      } else {
        $html = @file_get_contents($viewTemplate);
        @file_put_contents(VIEWS . $filename . DS . ($twig ? 'indexView.twig' : 'indexView.php'), $html);
      }
    }

    // Crear una vista por defecto
    Redirect::to($filename);
  }

  function post_model()
  {
    if (!Csrf::validate($_POST['csrf'])) {
      Flasher::deny();
      Redirect::back();
    }

    // Validar nombre de archivo
    $name     = clean($_POST["filename"]);
    $table    = clean($_POST["tabla"]);
    $scheme   = clean($_POST["esquema"]);
    $scheme_s = '';
    $filename = strtolower($name);
    $filename = str_replace(' ', '_', $filename);
    $filename = str_replace('.php', '', $filename);
    $keyword  = 'Model';
    $template = MODULES . 'bee' . DS . 'modelTemplate.txt';

    // Validar longitud del nombre
    if (strlen($name) < 4) {
      Flasher::error(sprintf('Ingresa un nombre de modelo válido por favor, <b>%s</b> es demasiado corto.', $name));
      Redirect::back();
    }

    // Validar longitud del nombre
    if (strlen($table) < 4 && !empty($table)) {
      Flasher::error(sprintf('Ingresa un nombre de tabla válido por favor, <b>%s</b> es demasiado corto.', $table));
      Redirect::back();
    }

    // Validar la existencia de un duplicado
    if (is_file(MODELS.$filename.$keyword.'.php')) {
      Flasher::error(sprintf('Ya existe el modelo %s.', $filename.$keyword));
      Redirect::back();
    }

    // Validar la existencia de la template de modelo
    if (!is_file($template)) {
      Flasher::error(sprintf('No existe la plantilla %s.', $template));
      Redirect::back();
    }
    
    // Cargar contenido del archivo
    $php     = @file_get_contents($template);
    $serch   = ['[[REPLACE]]', '[[REPLACE_TABLE]]'];
    $replace = [$filename, (empty($table) ? $filename : $table)];
    $php     = str_replace($serch, $replace, $php);

    // Validar si es necesario procesar el esquema de Modelo
    if (!empty($scheme)) {
      $scheme = str_replace([' ','.'], '', $scheme);
      $scheme = explode(',', $scheme);

      foreach ($scheme as $i => $s) {
        if ($i === 0) {
          $scheme_s .= sprintf("public $%s;\n", $s);
        } else {
          $scheme_s .= sprintf("\tpublic $%s;\n", $s);
        }
      }
    }

    // Reemplazar el esquema aunque no exista, para dejarlo en blanco en el archivo final
    $php = str_replace('[[REPLACE_SCHEME]]', $scheme_s, $php);

    // Guardar el contenido del Modelo en un nuevo archivo
    if (file_put_contents(MODELS.$filename.$keyword.'.php', $php) === false)  {
      Flasher::new(sprintf('Ocurrió un problema al crear el modelo %s.', $template), 'danger');
      Redirect::back();
    }

    Flasher::new(sprintf('Modelo <b>%s</b> creado con éxito.', $filename.$keyword));
    Redirect::back();
  }

  function post_view()
  {
    try {
      if (!Csrf::validate($_POST['csrf'])) {
        Flasher::deny();
        Redirect::back();
      }
  
      // Validar nombre de archivo
      array_map('clean', $_POST);
      $controller = $_POST["controller"];
      $viewName   = $_POST["viewName"];
      $viewName   = strtolower($viewName);
      $viewName   = str_replace(' ', '_', $viewName);
      $viewName   = str_replace('.php', '', $viewName);
      $viewName   = sanitize_input($viewName);
      $viewName   = remove_accents($viewName);
      $viewName   = normalize_string($viewName, true);
      $keyword    = 'View';
      $twig       = isset($_POST["usar-twig"]) ? true : false;
      $template   = MODULES . 'bee' . DS . ($twig ? 'viewTwigTemplate.txt' : 'viewTemplate.txt');
      $filename   = sprintf('%s%s.%s', $viewName, $keyword, $twig ? 'twig' : 'php');
  
      // Validar que sea un string válido
      if (!is_string($viewName)) {
        throw new Exception(sprintf('Ingresa un nombre de vista válido por favor.', $viewName));
      }
  
      // Validar longitud del nombre
      if (strlen($viewName) < 5) {
        throw new Exception(sprintf('Ingresa un nombre de vista válido por favor, <b>%s</b> es demasiado corto.', $viewName));
      }
  
      // Validar la existencia del controlador para prevenir remover un archivo existente
      $path = VIEWS . $controller . DS . $filename;
      if (is_file($path)) {
        throw new Exception(sprintf('Ya existe la vista <b>%s</b> del controlador <b>%s</b>.', $filename, $controller));
      }
  
      // Validar la existencia de la plantilla.txt para crear la vista
      if (!is_file($template)) {
        throw new Exception(sprintf('No existe la plantilla <b>%s</b>.', $template));
      }
  
      $html = @file_get_contents($template);
      if (@file_put_contents($path, $html) === false) {
        throw new Exception('Hubo un problema al crear la vista.');
      }
  
      Flasher::success(sprintf('La vista <b>%s</b> del controlador <b>%s</b> ha sido creada con éxito.', $filename, $controller));
      Redirect::back();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}