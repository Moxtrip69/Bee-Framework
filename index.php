<?php 

/**
 * Propiedades del framework
 * Desarrollado por el equipo de Joystick para todos
 * 
 * Sugerencias o pullrequest a:
 * soporte@joystick.com.mx
 * 
 * Roberto Orozco / roborozco@joystick.com.mx
 * 
 * Creado en nuestro curso dentro de la Academia:
 * https://www.academy.joystick.com.mx/bundles/pack-desarrollo-web-full-stack
 * 
 * ¡Gracias por todo su apoyo!
 *
 * Julio 2019 - Septiembre 2023 y actualizando
 */

$applicationRoot = __DIR__;
$server = $_SERVER;
$application = require __DIR__ . '/app/bootstrap/http.php';

// Capa heredada de despacho HTTP. La configuración y los servicios comunes
// ya fueron inicializados por el bootstrap independiente de Bee::fly().
require_once __DIR__ . '/app/classes/Bee.php';

// Ejecutar el framework bee
Bee::fly();
