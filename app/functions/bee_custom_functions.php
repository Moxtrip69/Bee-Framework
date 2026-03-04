<?php
// Funciones directamente del proyecto en curso

/**
 * Ejemplo para agregar endpoints autorizados para la API
 * Esto sólo es necesario si usarás más controladores a parte de apiController como endpoints de API
 * De lo contrario no requieres anexarlos a la lista de endpoints
 */
BeeHookManager::registerHook('init_set_up', 'set_up_routes');

function set_up_routes(Bee $instance)
{
  // Prueba ingresando a esta URL (depende de tu ubicación del proyecto): http://localhost:8848/Bee-Framework/reportes
  $instance->addEndpoint('reportes');
  $instance->addEndpoint('citas');
  $instance->addEndpoint('sucursales');

  $instance->addAjax('ajax2'); // http://localhost:8848/Bee-Framework/ajax2
}

/**
 * Cargar más funciones para segmentar código en diferentes archivos de funciones
 */
BeeHookManager::registerHook('after_functions_loaded', 'load_functions');

function load_functions()
{
  // Registra los archivos de funciones a cargar o incluir
  // require_once FUNCTIONS . 'miarchivodefunciones.php';
}