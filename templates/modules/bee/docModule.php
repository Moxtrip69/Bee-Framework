<h2 id="documentacion">Documentación</h2>
<p class="text-muted">Un framework hecho con pasión y sencillo de usar.</p>

<h4 id="instalacion"># Instalación</h4>
<p>Para ejecutar <b>Bee framework</b> por primera vez basta con editar el archivo <code>bee_config.php</code> dentro de la carpeta <code>config</code> y el archivo <code>settings.php</code> dentro de <code>core</code>.</p>
<p>Lo único que debes ajustar esencialmente es la ruta base o la constante <code>BASEPATH</code> del archivo <code>bee_config.php</code>, esta constante se encarga de indicar cuál es la ruta base o ruta principal del proyecto, tanto en desarrollo como en producción, por ejemplo: si tu proyecto se encuentra dentro de <code>htdocs/miempresa/</code> tu <code>BASEPATH</code> deberá usar <code>/miempresa/</code> como ruta en desarrollo.</p>
<p>Normalmente cuando no cargan bien los estilos o imágenes del sitio al abrir por primera vez la página, es porque no se ha configurado de forma correcta la ruta base del framework.</p>
<?php echo code_block("define('BASEPATH', IS_LOCAL ? '/miempresa/' : '/prod/');"); ?>

<p>La constante <code>IS_LOCAL</code> simplementa hace una validación para verificar si nos encontramos en un entorno de desarrollo, si es así se usará el <code>BASEPATH</code> con su valor de desarrollo de lo contrario el de producción, en estos momentos estas son tus configuraciones:</p>
<?php echo code_block('BASEPATH = ' . get_basepath()); ?>

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
<p><b>Bee framework</b> está diseñado en el patrón MVC (Modelo Vista Controlar) lo que separa en capas diferentes la lógica, la información y la interfaz, manteniendo cada área aislada y enfocada en su responsabilidad.</p>

<p>Para crear nuevas rutas basta con crear <code>controladores</code>, puedes hacerlo manualmente o usando la herramienta incluida llamada <code>Creator</code> puedes acceder a ella desde <a href="creator" target="_blank">aquí</a>, es decir si quieres una ruta por ejemplo para productos, basta con crear el controlador <code>productosController</code>, este debe ir dentro de la carpeta de controladores en <code>app/controllers</code>, cada nuevo controlador debe contar con mínimo un método llamado <code>index</code>, este será ejecutado por defecto al acceder a la ruta <code>productos</code>, si quisieras una ruta <code>productos/agregar</code>, basta con crear el método <code>agregar</code> dentro de <code>productosController</code>.</p>
<?php echo code_block('<?php class productosController extends Controller {...} ?>'); ?>

<h4 id="modelos"># Modelos</h4>
<p>Al igual que los controladores, puedes crear nuevos modelos con la herramienta <code>Creator</code>, recuerda usar las convenciones para nombrar tus modelos para mantener un estándar en tu código y estructura, por ejemplo para crear un modelo que se encargue de manipular registros de <b>productos</b>, puedes crear el modelo <code>productModel</code> o <code>productoModel</code>, en singular, esto te dará acceso a los métodos generales para manipular información de la base de datos:</p>
<?php echo code_block(file_get_contents(MODELS . 'productModel.php')); ?>

<p>Dentro de cada modelo tú podrás expandir la lógica y sus funcionalidades para que se adapten a tus necesidades o las de tu proyecto.</p>

<h4 id="coreFunc"># Funciones del core</h4>
<p>Dentro de Bee framework, en la carpeta <code>app/functions/</code> encontrarás el archivo <code>bee_core_functions.php</code> que contiene todas las funciones principales e indispinsables para el framework, no debes modificarlas directamente para evitar la pérdida de tus cambios después de alguna actualización.</p>
<p>Puedes ver la lista completa de las funciones revisando el código fuente en tu editor de código.</p>

<h4 id="customFunc"># Funciones personalizadas</h4>
<p>De igual manera existe el archivo <code>bee_custom_functions.php</code> que es ahí dónde podrás anexar todas las funciones que requieras para tu desarrollo, también podrás crear más archivos de funciones, sólo recuerda que deberás requerirlos dentro de <code>app/classes/Bee.php</code> para que tus funciones se ejecuten de forma correcta.</p>