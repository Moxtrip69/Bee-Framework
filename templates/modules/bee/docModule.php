<h1 id="documentacion">Documentación</h1>
<p class="text-muted">Un framework hecho con pasión y sencillo de usar.</p>

<h4 id="instalacion"># Instalación</h4>
<p>Para ejecutar <b>Bee framework</b> por primera vez basta con editar el archivo <code>bee_config.php</code> dentro de la carpeta <code>config</code> y el archivo <code>settings.php</code> dentro de <code>core</code>.</p>
<p>Lo único que debes ajustar esencialmente es la ruta base, para esto tenemos dos constantes definidas en nuestra versión <code>1.5.8</code>, la constante <code>DEV_PATH</code> y <code>LIVE_PATH</code> del archivo <code>bee_config.php</code>, estas constantes se encargan de indicar cuál es la ruta base o ruta principal del proyecto (<code>BASEPATH</code>), tanto en desarrollo como en producción</p>

<p>Por ejemplo: si tu proyecto se encuentra dentro de <code>htdocs/miempresa/</code> tu <code>DEV_PATH</code> deberá usar <code>'/miempresa/'</code> como ruta en desarrollo.</p>

<p>Y <code>LIVE_PATH</code> regularmente es sólo <code>'/'</code> ya que se suele poner el proyecto en la ruta principal del dominio, por lo que no requiere un subdirectorio.</p>
<p>Normalmente cuando no cargan bien los estilos o imágenes del sitio al abrir por primera vez la página, es porque no se ha configurado de forma correcta la ruta base del framework.</p>
<?php echo code_block(
"define('DEV_PATH'  , '/Bee-Framework/'); // Desarrollo
define('LIVE_PATH' , '/'); // Producción
define('BASEPATH'  , IS_LOCAL ? DEV_PATH : LIVE_PATH);"); ?>

<p>La constante <code>IS_LOCAL</code> simplementa hace una validación para verificar si nos encontramos en un entorno de desarrollo, si es así se usará el <code>BASEPATH</code> con su valor de desarrollo de lo contrario el de producción, en estos momentos estas son tus configuraciones:</p>
<?php echo code_block('DEV_PATH  = "' . DEV_PATH . '"'); ?>
<?php echo code_block('LIVE_PATH = "' . LIVE_PATH . '"'); ?>
<?php echo code_block('BASEPATH  = "' . get_basepath() . '"'); ?>

<h4 id="prepros"># Prepros</h4>
<p>Si has visto alguno de mis cursos, te has dado cuenta que en mi stack de herramientas está <b><a href="https://prepros.io/" target="_blank">Prepros</a></b>, una herramienta gratuita que me ayuda a agilizar mi trabajo de forma muy sencilla, no es obligatorio, pero <b>Bee framework</b> puede trabajar en conjunto con él, sólo basta con editar la constante <code>PREPROS</code> en el archivo <code>settings.php</code>, esta acepta un valor <code>Booleano</code> que puede ser <code>true</code> si usarás Prepros, o <code>false</code> si no lo usarás, lo único que hace es concatenar en tu URL base el <b>puerto</b> en el que Prepros escucha cambios y actualizaciones del código para hacer su trabajo. También recuerda actualizar la constante <code>PORT</code> para indicar cuál es el puerto que usa Prepros para trabajar (aparece en tu dashboard del proyecto), regularmente es el puerto <code>8848</code>:</p>
<?php echo code_block(
  "// Prepros 2023
define('PREPROS' , true);
define('PORT'    , '8848');"
) ?>

<p>Lo cual genera la siguiente URL base para tus enlaces y assets (revisa que sea correcta, de lo contrario ajusta tu configuración):</p>
<?php echo code_block('URL = ' . get_base_url()); ?>

<h4 id="db"># Base de datos</h4>
<p>Dentro de los archivos del framework encontrarás el archivo <code>db_beeframework.sql</code>, este contiene el código SQL para la estructura y creación de las diferentes tablas requeridas por Bee framework y sus funciones básicas, tú puedes añadir o modificar a tu gusto en cualquier momento, asegúrate de actualizar las credenciales y nombre de tu base de datos como se indica en el siguiente bloque.</p>

<p>En el archivo <code>settings.php</code> deberás configurar las credenciales de la base de datos local o de desarrollo:</p>
<?php echo code_block(
  "// Credenciales de la base de datos
// Set para conexión local o de desarrollo
define('LDB_ENGINE'  , 'mysql');
define('LDB_HOST'    , 'localhost');
define('LDB_NAME'    , 'db_beeframework');
define('LDB_USER'    , 'root');
define('LDB_PASS'    , '');
define('LDB_CHARSET' , 'utf8');"
); ?>
<p>Para configurar las credenciales de tu base de datos de producción, esto lo puedes hacer de igual manera pero en el archivo <code>bee_config.php</code>:</p>
<?php echo code_block(
  "// Set para conexión en producción o servidor real
define('DB_ENGINE'    , 'mysql');
define('DB_HOST'      , 'localhost');
define('DB_NAME'      , '___REMOTE DB___');
define('DB_USER'      , '___REMOTE DB___');
define('DB_PASS'      , '___REMOTE DB___');
define('DB_CHARSET'   , '___REMOTE CHARSET___');"
) ?>

<p>También deberás configurar en caso de ser necesario el controlador por defecto en la constante <code>DEFAULT_CONTROLLER</code> y el método por defecto (el que se ejecuta en caso de no especificar uno en la URL) <code>DEFAULT_METHOD</code>:</p>
<?php echo code_block(
  "// El controlador por defecto / el método por defecto / el controlador de errores por defecto
define('DEFAULT_CONTROLLER'       , 'bee');
define('DEFAULT_ERROR_CONTROLLER' , 'error');
define('DEFAULT_METHOD'           , 'index');"
); ?>

<h4 id="routing"># Routing</h4>
<p><b>Bee framework</b> está diseñado en el patrón MVC (Modelo Vista Controlador) lo que separa en capas diferentes la lógica, la información y la interfaz, manteniendo cada área aislada y enfocada en su responsabilidad.</p>

<p>Para crear nuevas rutas basta con crear <code>controladores</code>, puedes hacerlo manualmente o usando la herramienta incluida llamada <code>Creator</code>, accede a ella desde <a href="creator" target="_blank">aquí</a>, por ejemplo si quieres una ruta para <b>productos</b>, basta con crear el controlador <code>productosController</code>, este debe ir dentro de la carpeta de controladores en <code>app/controllers/</code>, cada nuevo controlador debe contar con mínimo un método llamado <code>index</code>, este será ejecutado por defecto al acceder a la ruta <code>productos</code>, si quisieras una ruta <code>productos/agregar/</code>, basta con crear el método <code>agregar</code> dentro de <code>productosController</code>.</p>
<?php echo code_block('<?php class productosController extends Controller {...} ?>'); ?>

<h4 id="modelos"># Modelos</h4>
<p>Al igual que los controladores, puedes crear nuevos modelos con la herramienta <code>Creator</code>, recuerda usar las convenciones para nombrar tus modelos para mantener un estándar en tu código y estructura, por ejemplo para crear un modelo que se encargue de manipular registros de <b>productos</b>, puedes crear el modelo <code>productModel</code> o <code>productoModel</code>, en singular, esto te dará acceso a los métodos generales para manipular información de la base de datos:</p>
<?php echo code_block(file_get_contents(MODELS . 'productoModel.php')); ?>

<p>Dentro de cada modelo tú podrás expandir la lógica y sus funcionalidades para que se adapten a tus necesidades o las de tu proyecto.</p>

<h4 id="coreFunc"># Funciones del core</h4>
<p>Dentro de Bee framework, en la carpeta <code>app/functions/</code> encontrarás el archivo <code>bee_core_functions.php</code> que contiene todas las funciones principales e indispinsables para el framework, no debes modificarlas directamente para evitar la pérdida de tus cambios después de alguna actualización.</p>
<p>Puedes ver la lista completa de las funciones revisando el código fuente en tu editor de código.</p>

<h4 id="customFunc"># Funciones personalizadas</h4>
<p>De igual manera existe el archivo <code>bee_custom_functions.php</code> que es ahí dónde podrás anexar todas las funciones que requieras para tu desarrollo, también podrás crear más archivos de funciones, sólo recuerda que deberás requerirlos dentro de <code>app/classes/Bee.php</code> para que tus funciones se ejecuten de forma correcta.</p>

<hr>

<h1 id="clasesIncorporadas">Clases incorporadas</h1>

<h4 id="formBuilder"># BeeFormBuilder</h4>
<p><a href="documentacion/form-builder">Ver más</a></p>

<h4 id="formBuilder"># BeeCartHandler</h4>
<p><a href="documentacion/cart-handler">Ver más</a></p>

<h4 id="formBuilder"># BeeMenuBuilder</h4>
<p><a href="documentacion/menu-builder">Ver más</a></p>

<hr>

<h1 id="hookList">Ganchos o hooks definidos</h1>
<p>Listado principal de hooks o ganchos disponibles hasta la versión <code>1.5.8</code> del framework:</p>

<h4># Dentro de Bee.php</h4>
<ul class="list-group mb-3">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">init_set_up</div>
      Después de inicializar las constantes del sistema y de Bee framework. Recibe como parámetro la instancia de <code>Bee</code>, esto hace posible utilizar sus métodos.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">after_functions_loaded</div>
      Después de que se han cargado todas las funciones del core y personalizadas.
    </div>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">after_init_globals</div>
      Después de inicializar las globales del sistema.
    </div>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">after_set_globals</div>
      Después de establecer valores de las globales del sistema.
    </div>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">after_init_custom</div>
      Después de ejecutar el método init_custom().
    </div>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">before_init_dispatch</div>
      Justo antes de procesar las rutas e instanciar el controlador solicitado. Recibe 3 parámetros: <code>$controller, $method, $params</code>.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
</ul>

<h4># Dentro de Controller.php</h4>
<ul class="list-group mb-3">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">is_regular_controller</div>
      Se ejecuta al finalizar el set up de cualquier controlador regular, útil para agregar nuevas claves iniciales a <code>$data</code>. Recibe la instancia de <code>Controller</code> actual.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">is_ajax_controller</div>
      Se ejecuta al finalizar el set up del controlador <code>AJAX</code>. Recibe la instancia de <code>Controller</code> actual.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">before_process_request_endpoint</div>
      Se ejecuta antes de procesar la petición y su contenido en un controlador de tipo <code>endpoint</code>. Recibe la instancia de <code>BeeHttp</code> de la petición.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">is_endpoint_controller</div>
      Se ejecuta al finalizar el set up del controlador de tipo <code>endpoint</code>. Recibe la instancia de <code>Controller</code> actual.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
</ul>

<h4># Dentro de View.php</h4>
<ul class="list-group mb-3">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">on_get_twig_filters</div>
      Se ejecuta dentro del método <code>getTwigFilters()</code>, permite registrar nuevos filtros de <b>Twig</b>. Recibe la instancia de <code>Twig\Environment</code> actual.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">on_get_twig_functions</div>
      Se ejecuta dentro del método <code>getTwigFunctions()</code>, permite registrar nuevas funciones de <b>Twig</b>. Recibe la instancia de <code>Twig\Environment</code> actual.
    </div>
    <span class="badge bg-danger rounded-pill">Importante</span>
  </li>
</ul>