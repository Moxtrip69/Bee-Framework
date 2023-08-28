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
    $this->addToData('products', productModel::all_paginated());
    $this->setView('index');
    $this->render();
  }

  function producto($slug)
  {
    try {
      if (!$product = productModel::by_slug($slug)) {
        throw new Exception('No existe el producto que buscas.');
      }

      register_styles([CSS . 'store.css'], 'Estilos de la página de detalles del producto');

      $this->setTitle($product['name']);
      $this->addToData('p', $product);
      $this->setView('producto');
      $this->render();

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      http_response_code(404);
      Redirect::back('tienda');
    }
  }
}