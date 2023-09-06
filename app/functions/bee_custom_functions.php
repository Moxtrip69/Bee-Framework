<?php
// Funciones directamente del proyecto en curso

use Twig\Environment;
use Twig\TwigFunction;

/**
 * Carga el archivo de funciones para las clases en vivo, tutoriales y streams de Joystick
 * Puedes borrar todo esto sin problema alguno o usarlo cómo referencia para tus proyectos
 *
 * @return void
 */
function load_joystick_functions()
{
  require_once FUNCTIONS . 'puedes_borrarlas.php';
}

function setUpRoutes(Bee $instance)
{
  $instance->addEndpoint('reportes'); // agrega el endpoint reportes como autorizado para consumirse como un endpoint, deberás crear el controlador y definirlo como endpoint también

  $instance->addEndpoint('citas');
  $instance->addEndpoint('sucursales');

  // Prueba ingresando a esta URL (depende de tu ubicación del proyecto): http://localhost:8848/Bee-Framework/reportes
}

/**
 * Se ejecuta el hook después de la carga de todas las funciones del core
 */
BeeHookManager::registerHook('after_functions_loaded', 'load_joystick_functions');

/**
 * Ejemplo para agregar endpoints autorizados para la API
 * Esto sólo es necesario si usarás más controladores a parte de apiController como endpoints de API
 * De lo contrario no requieres anexarlos a la lista de endpoints
 */
BeeHookManager::registerHook('init_set_up', 'setUpRoutes');