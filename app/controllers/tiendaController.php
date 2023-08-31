<?php
/**
 * Plantilla general de controladores
 * @version 1.0.5
 *
 * Controlador de tienda
 */
class tiendaController extends Controller implements ControllerInterface {

  private $cartHandler;
  private $cart;

  function __construct()
  {
    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();
  }
  
  function index()
  {
    $this->setTitle('Bienvenido a la tienda');
    $this->addToData('productos', productoModel::all_paginated());
    $this->setView('index');
    $this->render();
  }

  function producto($slug)
  {
    try {
      if (!$producto = productoModel::by_slug($slug)) {
        throw new Exception('No existe el producto que buscas.');
      }

      /**
       * Estilos sólo para la página de producto
       */
      register_styles([CSS . 'store.css'], 'Estilos de la página de detalles del producto');

      $this->setTitle($producto['nombre']);
      $this->addToData('p', $producto);
      $this->setView('producto');
      $this->render();

    } catch (Exception $e) {
      http_response_code(404);
      Flasher::error($e->getMessage());
      Redirect::back('tienda');
    }
  }
}