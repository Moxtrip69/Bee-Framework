<?php 

/**
 * Constantes migradas de bee_config.php
 * a este archivo para cuando se deba realizar una actualización del sistema
 * o corrección, las credenciales de la base de datos no queden expuestas ni
 * sean modificadas en el proceso por accidente así como el basepath y otras constantes que requieran
 * configuración especial en producción
 */

define('IS_LOCAL'   , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('BASEPATH'   , IS_LOCAL ? '/Bee-Framework/' : '____EL BASEPATH EN PRODUCCIÓN___');
define('IS_DEMO'    , false);

// Set para conexión en producción o servidor real
define('DB_ENGINE'  , 'mysql');
define('DB_HOST'    , 'localhost');
define('DB_NAME'    , '___REMOTE DB___');
define('DB_USER'    , '___REMOTE DB___');
define('DB_PASS'    , '___REMOTE DB___');
define('DB_CHARSET' , '___REMOTE CHARTSET___');