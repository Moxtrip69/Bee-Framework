<?php 

/**
 * Controlador para generar modelos y controladores de forma dinámica
 */
class creatorController extends Controller {
  function __construct()
  {
  }
  
  function index() {
    View::render('index', ['title' => 'Crea un nuevo archivo']);
  }

  function controller()
  {
    View::render('controller', ['title' => 'Nuevo controlador']);
  }

  function model()
  {
    View::render('model', ['title' => 'Nuevo modelo']);
  }

  function post_controller()
  {
    if (!Csrf::validate($_POST['csrf'])) {
      Flasher::new('Acceso no autorizado.', 'danger');
      Redirect::back();
    }

    // Validar nombre de archivo
    $filename = clean($_POST['filename']);
    $filename = strtolower($filename);
    $filename = str_replace(' ', '_', $filename);
    $filename = str_replace('.php', '', $filename);
    $keyword  = 'Controller';
    $template = MODULES.'controllerTemplate.txt';

    // Validar la existencia del controlador para prevenir remover un archivo existente
    if (is_file(CONTROLLERS.$filename.$keyword.'.php')) {
      Flasher::new(sprintf('Ya existe el controladores %s.', $filename.$keyword), 'danger');
      Redirect::back();
    }

    // Validar la existencia de la plantilla .txt para crear el controlador
    if (!is_file($template)) {
      Flasher::new(sprintf('No existe la plantilla %s.', $template), 'danger');
      Redirect::back();
    }
    
    // Cargar contenido del archivo
    $php = @file_get_contents($template);
    $php = str_replace('[[REPLACE]]', $filename, $php);
    if (file_put_contents(CONTROLLERS.$filename.$keyword.'.php', $php) === false)  {
      Flasher::new(sprintf('Ocurrió un problema al crear el controlador %s.', $template), 'danger');
      Redirect::back();
    }

    // Crear el folder en carpeta vistas
    if (!is_dir(VIEWS.$filename)) {
      mkdir(VIEWS.$filename);

      $body = 
      '<?php require_once INCLUDES.\'inc_header.php\'; ?>
      <div class="container">
        <div class="row">
          <div class="col-6 text-center offset-xl-3">
            <a href="<?php echo URL; ?>"><img src="<?php echo IMAGES.\'bee_logo.png\' ?>" alt="Bee framework" class="img-fluid" style="width: 200px;"></a>
            <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
            <!-- contenido -->
            <h1><?php echo $d->msg; ?></h1>
            <!-- ends -->
          </div>
        </div>
      </div>
      
      <?php require_once INCLUDES.\'inc_footer.php\'; ?>';
      
      @file_put_contents(VIEWS.$filename.DS.'indexView.php', $body);
    }

    // Crear una vista por defecto
    Redirect::to($filename);
  }

  function post_model()
  {
    if (!Csrf::validate($_POST['csrf'])) {
      Flasher::new('Acceso no autorizado.', 'danger');
      Redirect::back();
    }

    // Validar nombre de archivo
    $filename = clean($_POST['filename']);
    $filename = strtolower($filename);
    $filename = str_replace(' ', '_', $filename);
    $filename = str_replace('.php', '', $filename);
    $keyword  = 'Model';
    $template = MODULES.'modelTemplate.txt';

    if (is_file(CONTROLLERS.$filename.$keyword.'.php')) {
      Flasher::new(sprintf('Ya existe el modelo %s.', $filename.$keyword), 'danger');
      Redirect::back();
    }

    if (!is_file($template)) {
      Flasher::new(sprintf('No existe la plantilla %s.', $template), 'danger');
      Redirect::back();
    }
    
    // Cargar contenido del archivo
    $php = @file_get_contents($template);
    $php = str_replace('[[REPLACE]]', $filename, $php);
    if (file_put_contents(MODELS.$filename.$keyword.'.php', $php) === false)  {
      Flasher::new(sprintf('Ocurrió un problema al crear el modelo %s.', $template), 'danger');
      Redirect::back();
    }

    Flasher::new(sprintf('Modelo %s creado con éxito.', $filename.$keyword));
    Redirect::back();
  }
}