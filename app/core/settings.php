<?php
/**
 * Creado y desarrollado por Joystick México SAS de CV
 * Bee Framework
 * 2023
 * 
 * soporte@joystick.com.mx
 * 
 * Únete gratis en www.joystick.com.mx
 * 
 * Template de configuración inicial
 * @version 1.0.2
 */

// Definir el uso horario o timezone del sistema
date_default_timezone_set('America/Mexico_City');

/**
 * Keys para consumo de la API de esta instancia de bee framework
 * puedes regenerarlas en bee/generate
 * @since 1.1.4
 */
define('API_PUBLIC_KEY'          , '03d427-c4034c-d2dc71-373b10-36da67');
define('API_PRIVATE_KEY'         , '51362e-0cb1b9-f9c183-17b0a3-1a002e');

/**
 * Migrados desde config/bee_config.php a core/settings.php
 * 
 * Salt utilizada para agregar seguridad al hash de contraseñas dependiendo el uso requerido
 */
define('AUTH_SALT'               , '$2y$10$WNRjHI4M7E/rMJlKYj2Im.0Pv5qlhoRTeT6jCoFzDAAHx69gyAMS.');
define('NONCE_SALT'              , '');

/**
 * Define si es requerida autenticación para consumir los recursos de la API
 * programáticamente se define que recursos son accesibles sin autenticación
 * 
 * Por defecto true | false para consumir la API sin autenticación | no recomendado
 * 
 * @since 1.1.4
 * 
 */
define('API_AUTH'                , true);

// Lenguaje
define('SITE_LANG'               , 'es');

// Prepros 2023
define('PREPROS'                 , true);              // Activar en caso de trabajar el desarrollo en prepros como servidor local
define('PORT'                    , '8848');            // Puerto por defecto de Prepros 2020 >

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
define('CSS_FRAMEWORK'           , 'bs_flatly'); // opciones disponibles: bs o bs5 = Bootstrap 5 | bl = Bulma | fn = Foundation

// Utilidades
define('JQUERY'                  , true);  // define si es requerido jQuery para el sitio
define('VUEJS'                   , true);  // define si es requerido Vue js 3 para el sitio | CDN
define('AXIOS'                   , false); // define si es requerido Axios para peticiones HTTP
define('SWEETALERT2'             , true);  // define si es requerido sweetalert2 por defecto
define('TOASTR'                  , true);  // define si es requerido Toastr para notificaciones con Javascript
define('WAITME'                  , true);  // define si es requerido WaitMe
define('LIGHTBOX'                , false); // define si es requerido Lightbox

/**
 * Motor de templates con Twig 3.6
 * @since 1.5.8
 */
define('USE_TWIG'                , false); // define si será usado Twig por defecto para renderizar las vistas

// Datos de la empresa / negocio / sistema
define('SITE_CHARSET'            , 'UTF-8');
define('SITE_NAME'               , 'EmpresaCool');    // Nombre del sitio
define('SITE_VERSION'            , '1.0.0');          // Versión del sitio
define('SITE_LOGO'               , 'logo.png');       // Nombre del archivo del logotipo base
define('SITE_FAVICON'            , 'favicon.ico');    // Nombre del archivo del favicon base
define('SITE_DESC'               , 'Un framework hecho con pasión y amor, flexible, sencillo y fácil de implementar desarrollado por la Academia de Joystick.'); // Descripción meta del sitio

// Sesiones de usuario persistentes
define('BEE_USERS_TABLE'         , 'bee_users');         // Nombre de la tabla para autenticación de usuarios
define('BEE_COOKIES'             , true);                // Es utilizada para determinar si se usarán sesiones persistentes con cookies en el sistema
define('BEE_COOKIE_ID'           , 'bee__cookie_id');    // Nombre del cookie para el identificador de usuario
define('BEE_COOKIE_TOKEN'        , 'bee__cookie_tkn');   // Nombre del cookie para el token generado para usuario
define('BEE_COOKIE_LIFETIME'     , 60 * 60 * 24 * 7);    // Duración o vida de un cookie para cada usuario, por defecto 1 semana
define('BEE_COOKIE_PATH'         , '/');
define('BEE_COOKIE_DOMAIN'       , '');

// Configuración de correos electrónicos
define('PHPMAILER_EXCEPTIONS'    , true);                // Mantener activo para recibir excepciones en errores de Phpmailer
define('PHPMAILER_SMTP'          , false);               // Activar uso de cuenta SMTP para envío de correos true o false
define('PHPMAILER_DEBUG'         , false);               // Solo activar si es necesario el log verboso para debug
define('PHPMAILER_HOST'          , 'smtp.example.com');  // Dominio o servidor SMTP
define('PHPMAILER_AUTH'          , true);                // Autenticar con SMTP true o false
define('PHPMAILER_USERNAME'      , 'user@example.com');  // Usuario de la cuenta
define('PHPMAILER_PASSWORD'      , '123secret');         // Password de la cuenta
define('PHPMAILER_SECURITY'      , 'tls');               // Tipo de seguridad, opciones tls o ssl
define('PHPMAILER_PORT'          , '465');               // Puerto de conexión SMTP -- 587 hotmail -- 465 gmail
define('PHPMAILER_TEMPLATE'      , 'emailTemplate');     // Plantilla por defecto de correo electrónico

// Puerto y la URL del sitio
define('PROTOCOL'                , isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"); // Detectar si está en HTTPS o HTTP
define('HOST'                    , $_SERVER['HTTP_HOST'] === 'localhost' ? (PREPROS ? 'localhost:' . PORT : 'localhost') : $_SERVER['HTTP_HOST']); // Dominio o host localhost.com tudominio.com
define('REQUEST_URI'             , $_SERVER["REQUEST_URI"]);               // Parámetros y ruta requerida
define('URL'                     , PROTOCOL . '://' . HOST . BASEPATH);    // URL del sitio
define('CUR_PAGE'                , PROTOCOL . '://' . HOST . REQUEST_URI); // URL actual incluyendo parámetros get

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

// Rutas de recursos y assets absolutos
define('IMAGES_PATH'             , ROOT . 'assets' . DS . 'images' . DS);

// Rutas de archivos o assets con base URL
define('ASSETS'                  , URL . 'assets/');
define('CSS'                     , ASSETS . 'css/');
define('FAVICON'                 , ASSETS . 'favicon/');
define('FONTS'                   , ASSETS . 'fonts/');
define('IMAGES'                  , ASSETS . 'images/');
define('JS'                      , ASSETS . 'js/');
define('COMPONENTS'              , JS . 'components/');
define('PLUGINS'                 , ASSETS . 'plugins/');
define('UPLOADS'                 , ROOT . 'assets' . DS . 'uploads' . DS);
define('UPLOADED'                , ASSETS . 'uploads/');

// Credenciales de la base de datos
// Set para conexión local o de desarrollo
define('LDB_ENGINE'              , 'mysql');
define('LDB_HOST'                , 'localhost');
define('LDB_NAME'                , 'db_beeframework');
define('LDB_USER'                , 'root');
define('LDB_PASS'                , '');
define('LDB_CHARSET'             , 'utf8');

// El controlador por defecto / el método por defecto / el controlador de errores por defecto
define('DEFAULT_CONTROLLER'      , 'bee');
define('DEFAULT_ERROR_CONTROLLER', 'error');
define('DEFAULT_METHOD'          , 'index');

// Para uso de Google Maps
define('GMAPS'                   , '__TOKEN__');