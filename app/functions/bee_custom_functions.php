<?php
// Funciones directamente del proyecto en curso

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

/**
 * Se ejecuta el hook después de la carga de todas las funciones del core
 */
BeeHookManager::registerHook('after_functions_loaded', 'load_joystick_functions');