<?php 
/**
 * Creado y desarrollado por Joystick México SAS de CV
 * Bee Framework
 * 2025
 * 
 * soporte@joystick.com.mx
 * 
 * Únete gratis en www.joystick.com.mx
 * 
 * Template de configuración inicial
 * @version 1.6.0
 */

use Dotenv\Dotenv;

// Las rutas de directorios y archivos
define('DS'                      , DIRECTORY_SEPARATOR);
define('ROOT'                    , getcwd() . DS);

define('APP'                     , ROOT . 'app' . DS);
define('CLASSES'                 , APP . 'classes' . DS);
define('CONFIG'                  , APP . 'config' . DS);
define('CORE'                    , APP . 'core' . DS);
define('CONTROLLERS'             , APP . 'controllers' . DS);
define('FUNCTIONS'               , APP . 'functions' . DS);
define('MODELS'                  , APP . 'models' . DS);
define('LOGS'                    , APP . 'logs' . DS);

define('TEMPLATES'               , ROOT . 'templates' . DS);
define('INCLUDES'                , TEMPLATES . 'includes' . DS);
define('MODULES'                 , TEMPLATES . 'modules' . DS);
define('VIEWS'                   , TEMPLATES . 'views' . DS);

// Ruta de imágenes en disco
define('IMAGES_PATH'             , ROOT . 'assets' . DS . 'images' . DS);

// Carga de variables de entorno en .env
$dotenv = Dotenv::createImmutable(CONFIG);
$dotenv->safeLoad();

// Guardar todos los valores de configuración en settings
$this->settings = $_ENV;

// Puerto y la URL del sitio
/**
 * Define si es requerida autenticación para consumir los recursos de la API
 * programáticamente se define que recursos son accesibles sin autenticación
 * 
 * Por defecto true | false para consumir la API sin autenticación | no recomendado
 * 
 * @since 1.1.4
 * 
 */
define('API_AUTH'                , filter_var($_ENV['API_PROTECTED'], FILTER_VALIDATE_BOOLEAN));

// URL base de todo el sistema
/**
 * Constantes migradas de settings.php
 * a este archivo para cuando se deba realizar una actualización del sistema
 * o corrección, las credenciales de la base de datos no queden expuestas ni
 * sean modificadas en el proceso por accidente así como el basepath y otras constantes que requieran
 * configuración especial en producción
 */
define('IS_LOCAL'                , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('PORT'                    , $_ENV['APP_PORT']); // Puerto personalizado
define('PROTOCOL'                , isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"); // Detectar si está en HTTPS o HTTP
define('HOST'                    , sprintf('%s%s', IS_LOCAL ? 'localhost' : $_SERVER['HTTP_HOST'], !empty(PORT) && IS_LOCAL ? ':' . PORT : '')); // Dominio o host localhost.com tudominio.com
define('REQUEST_URI'             , $_SERVER["REQUEST_URI"]);               // Parámetros y ruta requerida
define('DEV_PATH'                , $_ENV['APP_DEV_PATH']); // Ruta del proyecto en desarrollo después de htdocs o www
define('LIVE_PATH'               , $_ENV['APP_LIVE_PATH']); // Ruta del proyecto en producción
define('BASEPATH'                , IS_LOCAL ? DEV_PATH : LIVE_PATH);
define('URL'                     , PROTOCOL . '://' . HOST . BASEPATH);    // URL del sitio
define('CUR_PAGE'                , PROTOCOL . '://' . HOST . REQUEST_URI); // URL actual incluyendo parámetros get

// URL absolutas de archivos o assets
define('ASSETS'                  , URL . 'assets/');
define('CSS'                     , ASSETS . 'css/');
define('FAVICON'                 , ASSETS . 'favicon/');
define('FONTS'                   , ASSETS . 'fonts/');
define('IMAGES'                  , ASSETS . 'images/');
define('JS'                      , ASSETS . 'js/');
define('COMPONENTS'              , JS . 'components/');
define('PLUGINS'                 , ASSETS . 'plugins/');
define('UPLOADS'                 , ROOT . 'assets' . DS . 'uploads' . DS); // Carga de paths
define('UPLOADED'                , ASSETS . 'uploads/'); // Carga con URL absolutas

// Definir el uso horario o timezone del sistema
date_default_timezone_set($_ENV['APP_TIMEZONE']);

// Lenguaje principal del sistema
define('SITE_LANG'               , $_ENV['APP_LANG']);

// En caso de implementación de pagos en línea para definir si se está trabajando con pasarelas en modo sandbox / prueba o producción
define('SANDBOX'                 , filter_var($_ENV['IS_SANDBOX'], FILTER_VALIDATE_BOOLEAN)); 

// Si es requerida añadir funcionalidad DEMO en tu sistema, puedes usarlo con esta constante
define('IS_DEMO'                 , filter_var($_ENV['IS_DEMO'], FILTER_VALIDATE_BOOLEAN));

/**
 * Keys para consumo de la API de esta instancia de bee framework
 * puedes regenerarlas en bee/generate
 * @since 1.1.4
 */
define('API_PUBLIC_KEY'          , $_ENV['API_PUBLIC_KEY']);
define('API_PRIVATE_KEY'         , $_ENV['API_PRIVATE_KEY']);

/**
 * Migrados desde config/bee_config.php a core/settings.php
 * 
 * Salt utilizada para agregar seguridad al hash de contraseñas dependiendo el uso requerido
 */
define('AUTH_SALT'               , $_ENV['AUTH_SALT']);
define('NONCE_SALT'              , $_ENV['NONCE_SALT']);

/**
 * @since 1.6.0
 * 
 * Temas disponibles para bootstrap 5 solamente
 * bs_darkly
 * bs_flatly
 * bs_lux
 * bs_lumen
 * bs_litera
 * bs_vapor
 * bs_zephyr
 */
define('CSS_FRAMEWORK'           , $_ENV["CSS_FRAMEWORK"]); // opciones disponibles: bs o bs5 = Bootstrap 5 | bl = Bulma | fn = Foundation

// Utilidades
define('JQUERY'                  , filter_var($_ENV['USE_JQUERY'], FILTER_VALIDATE_BOOLEAN));  // define si es requerido jQuery para el sitio
define('VUEJS'                   , filter_var($_ENV['USE_VUEJS'], FILTER_VALIDATE_BOOLEAN));  // define si es requerido Vue js 3 para el sitio | CDN
define('AXIOS'                   , filter_var($_ENV['USE_AXIOS'], FILTER_VALIDATE_BOOLEAN)); // define si es requerido Axios para peticiones HTTP
define('SWEETALERT2'             , filter_var($_ENV['USE_SWEETALERT2'], FILTER_VALIDATE_BOOLEAN));  // define si es requerido sweetalert2 por defecto
define('TOASTR'                  , filter_var($_ENV['USE_TOASTR'], FILTER_VALIDATE_BOOLEAN));  // define si es requerido Toastr para notificaciones con Javascript
define('WAITME'                  , filter_var($_ENV['USE_WAITME'], FILTER_VALIDATE_BOOLEAN));  // define si es requerido WaitMe
define('LIGHTBOX'                , filter_var($_ENV['USE_LIGHTBOX'], FILTER_VALIDATE_BOOLEAN)); // define si es requerido Lightbox

/**
 * Motor de templates con Twig 3.6
 * @since 1.5.8
 */
define('USE_TWIG'                , filter_var($_ENV['USE_TWIG'], FILTER_VALIDATE_BOOLEAN)); // define si será usado Twig por defecto para renderizar las vistas

// Datos de la empresa / negocio / sistema
define('SITE_CHARSET'            , $_ENV["APP_CHARSET"]);
define('SITE_NAME'               , $_ENV["APP_NAME"]);    // Nombre del sitio
define('SITE_VERSION'            , $_ENV["APP_VERSION"]); // Versión del sitio
define('SITE_LOGO'               , $_ENV["APP_LOGO"]);    // Nombre del archivo del logotipo base
define('SITE_FAVICON'            , $_ENV["APP_FAVICON"]); // Nombre del archivo del favicon base
define('SITE_DESC'               , $_ENV["APP_DESC"]);    // Descripción meta del sitio

// Set para conexión local o de desarrollo
define('LDB_ENGINE'              , $_ENV["LDB_ENGINE"]);
define('LDB_HOST'                , $_ENV["LDB_HOST"]);
define('LDB_NAME'                , $_ENV["LDB_NAME"]);
define('LDB_USER'                , $_ENV["LDB_USER"]);
define('LDB_PASS'                , $_ENV["LDB_PASS"]);
define('LDB_CHARSET'             , $_ENV["LDB_CHARSET"]);

// Set para conexión en producción o servidor real
define('DB_ENGINE'               , $_ENV["DB_ENGINE"]);
define('DB_HOST'                 , $_ENV["DB_HOST"]);
define('DB_NAME'                 , $_ENV["DB_NAME"]);
define('DB_USER'                 , $_ENV["DB_USER"]);
define('DB_PASS'                 , $_ENV["DB_PASS"]);
define('DB_CHARSET'              , $_ENV["DB_CHARSET"]);

// El controlador por defecto / el método por defecto / el controlador de errores por defecto
define('DEFAULT_CONTROLLER'      , $_ENV["DEFAULT_CONTROLLER"]);
define('DEFAULT_ERROR_CONTROLLER', $_ENV["DEFAULT_ERROR_CONTROLLER"]);
define('DEFAULT_METHOD'          , $_ENV["DEFAULT_METHOD"]);

// Tiempo de caducación de cookies
$periods = [
  'hour'  => 60 * 60,
  'day'   => 60 * 60 * 24,
  'week'  => 60 * 60 * 24 * 7,
  'month' => 60 * 60 * 24 * 30,
  'year'  => 60 * 60 * 24 * 365
];

// Sesiones de usuario persistentes
define('BEE_USERS_TABLE'         , $_ENV["BEE_USERS_TABLE"]);
define('BEE_COOKIE_ID'           , $_ENV["COOKIE_ID_NAME"]);
define('BEE_COOKIE_TOKEN'        , $_ENV["COOKIE_TOKEN_NAME"]);
define('BEE_COOKIE_LIFETIME'     , $periods[$_ENV["COOKIE_LIFETIME"]] ?? 604800);
define('BEE_COOKIE_PATH'         , $_ENV["COOKIE_PATH"]);
define('BEE_COOKIE_DOMAIN'       , $_ENV["COOKIE_DOMAIN"]);
define('BEE_COOKIE_HTTPS'        , filter_var($_ENV['COOKIE_HTTPS'], FILTER_VALIDATE_BOOLEAN));

// Configuración de correos electrónicos
define('PHPMAILER_EXCEPTIONS'    , filter_var($_ENV['PHPMAILER_EXCEPTIONS'], FILTER_VALIDATE_BOOLEAN));
define('PHPMAILER_SMTP'          , filter_var($_ENV['PHPMAILER_SMTP'], FILTER_VALIDATE_BOOLEAN));
define('PHPMAILER_DEBUG'         , filter_var($_ENV['PHPMAILER_DEBUG'], FILTER_VALIDATE_BOOLEAN));
define('PHPMAILER_HOST'          , $_ENV['PHPMAILER_HOST']);
define('PHPMAILER_AUTH'          , filter_var($_ENV['PHPMAILER_AUTH'], FILTER_VALIDATE_BOOLEAN));
define('PHPMAILER_USERNAME'      , $_ENV['PHPMAILER_USERNAME']);
define('PHPMAILER_PASSWORD'      , $_ENV['PHPMAILER_PASSWORD']);
define('PHPMAILER_SECURITY'      , $_ENV['PHPMAILER_SECURITY']);
define('PHPMAILER_PORT'          , $_ENV['PHPMAILER_PORT']);
define('PHPMAILER_TEMPLATE'      , $_ENV['PHPMAILER_TEMPLATE']);