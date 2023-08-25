<?php 

/**
 * Constantes migradas de settings.php
 * a este archivo para cuando se deba realizar una actualización del sistema
 * o corrección, las credenciales de la base de datos no queden expuestas ni
 * sean modificadas en el proceso por accidente así como el basepath y otras constantes que requieran
 * configuración especial en producción
 */
define('IS_LOCAL'     , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('BASEPATH'     , IS_LOCAL ? '/Bee-Framework/' : '____BASEPATH EN PRODUCCIÓN___'); // Debe ser cambiada a la ruta de tu proyecto en producción y desarrollo
define('IS_DEMO'      , false); // Si es requerida añadir funcionalidad DEMO en tu sistema, puedes usarlo con esta constante

// En caso de implementación de pagos en línea para definir si se está trabajando con pasarelas en modo sandbox / prueba o producción
define('SANDBOX'      , true); // true o false para ambientes live/producción o sandbox/pruebas

// Set para conexión en producción o servidor real
define('DB_ENGINE'    , 'mysql');
define('DB_HOST'      , 'localhost');
define('DB_NAME'      , '___REMOTE DB___');
define('DB_USER'      , '___REMOTE DB___');
define('DB_PASS'      , '___REMOTE DB___');
define('DB_CHARSET'   , '___REMOTE DB___');