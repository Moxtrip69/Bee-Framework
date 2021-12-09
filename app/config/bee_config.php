<?php 

/**
 * Constantes migradas de settings.php
 * a este archivo para cuando se deba realizar una actualización del sistema
 * o corrección, las credenciales de la base de datos no queden expuestas ni
 * sean modificadas en el proceso por accidente así como el basepath y otras constantes que requieran
 * configuración especial en producción
 */
define('IS_LOCAL'     , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('BASEPATH'     , IS_LOCAL ? '/Bee-Framework/' : '____EL BASEPATH EN PRODUCCIÓN___'); // Debe ser cambiada a la ruta de tu proyecto en producción y desarrollo
define('IS_DEMO'      , false);

// En caso de implementación de pagos en línea para definir si se está trabajando con pasarelas en modo sanbox / prueba o producción
define('SANDBOX'      , true); // live or sandbox

// Set para conexión en producción o servidor real
define('DB_ENGINE'    , 'mysql');
define('DB_HOST'      , 'localhost');
define('DB_NAME'      , '___REMOTE DB___');
define('DB_USER'      , '___REMOTE DB___');
define('DB_PASS'      , '___REMOTE DB___');
define('DB_CHARSET'   , '___REMOTE CHARTSET___');

// Para uso de Google Maps
define('GMAPS'        , '__TOKEN__');

// Nombres de cookies para autentificación de usuarios
// el valor puede ser cambiado en caso de utilizar
// multiples instancias de Bee para proyectos diferentes y los cookies no representen un problema por el nombre repetido
define('AUTH_TKN_NAME', 'bee__cookie_tkn'); // deprecado en versión 1.1.4
define('AUTH_ID_NAME' , 'bee__cookie_id');  // deprecado en versión 1.1.4

// Salt utilizada para agregar seguridad al hash de contraseñas dependiendo el uso requerido
define('AUTH_SALT'    , '$2y$10$WNRjHI4M7E/rMJlKYj2Im.0Pv5qlhoRTeT6jCoFzDAAHx69gyAMS.');




