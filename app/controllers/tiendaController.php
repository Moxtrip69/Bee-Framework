<?php
/**
 * Plantilla general de controladores
 * @version 1.0.5
 *
 * Controlador de tienda
 */
class tiendaController extends Controller {

  private $cartHandler;
  private $cart;

  function __construct()
  {
    // Carga del carrito de compras
    $this->cartHandler = new BeeCartHandler();
    $this->cart        = $this->cartHandler->loadCart();
  }
  
  function index()
  {
    $data = 
    [
      'title'    => 'Bienvenido a la tienda',
      'cart'     => $this->cart,
      'products' => productModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function producto($slug)
  {
    try {
      if (!$product = productModel::by_slug($slug)) {
        throw new Exception('No existe el producto que buscas.');
      }

      $data = 
      [
        'title' => $product['name'],
        'cart'  => $this->cart,
        'p'     => $product
      ];

      register_styles([CSS . 'store.css'], 'Estilos de la página de detalles del producto');

      View::render('producto', $data);

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      http_response_code(404);
      Redirect::back('tienda');
    }
  }
}