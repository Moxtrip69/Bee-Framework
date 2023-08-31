<?php
/**
 * Plantilla general de controladores
 * @version 1.0.5
 *
 * Controlador de carrito
 */
class carritoController extends Controller implements ControllerInterface {

  private $cartHandler;
  private $cart;

  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //   Flasher::new('Debes iniciar sesión primero.', 'danger');
    //   Redirect::to('login');
    // }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();

    // Dejar instanciado nuestro handler del carrito
    $this->cartHandler = new BeeCartHandler();

    // Verificar si existe un cupón de descuento en la URL
    $couponCode = isset($_GET["couponCode"]) ? clean(strtoupper($_GET["couponCode"])) : null;
    if ($couponCode !== null) {
      // TODO: Verificar de la base de datos y ver si existe el cupón ingresado
      // TODO: Crear modelos y estructura para almacenar los cupones
      // APOYOEDU2023 es un cupón de la academia válido, usar como ejemplo
      $validCouponCode = 'APOYOEDU2023';
      if ($couponCode == $validCouponCode) {
        // Crear el cupón de descuento como ejemplo
        $coupon      = new BeeCartCoupon($couponCode, 'Apoyo educativo de la Academia.', 50, 'percentage', strtotime('+5 days'));

        // Agregar el cupón
        $this->cartHandler->addCoupon($coupon);

        // Informar al usuario
        Flasher::success(sprintf('Cupón <b>%s</b> aplicado con éxito en tu carrito.', $couponCode));
        Redirect::back('carrito');
      }
    }
  }
  
  function index()
  {
    $this->addToData('title', 'Carrito de compras');
    $this->setView('index');
    $this->render();
  }

  function agregar($itemId = null, $quantity = 1)
  {
    try {
      // Validar que exista el producto en la base de datos
      if (!$producto = productoModel::by_id($itemId)) {
        throw new Exception('No existe el producto o no está disponible.');
      }

      // Verificar si se rastrea el stock y está disponible para la compra
      $stock      = (int) $producto['stock'];
      $trackStock = (int) $producto['rastrear_stock'] === 1;
      $price      = $producto['precio_comparacion'] < $producto['precio'] ? $producto['precio_comparacion'] : $producto['precio'];

      // TODO: Verificar cuantas unidades hay del mismo producto en carrito, sumar las que se quieren anexar y validar que no excedan del stock disponible

      if ($trackStock && $stock < 1) {
        throw new Exception('El producto que quieres agregar está agotado.');
      }

      if ($quantity > $stock && $trackStock) {
        throw new Exception(sprintf('Sólo hay <b>%s</b> unidades disponibles a la venta de <b>%s</b> requeridas.', $stock, $quantity));
      }

      // Agregar al carrito de compras
      $item = new BeeCartItem($itemId, $producto['nombre'], $price, $quantity, $producto['descripcion'], $producto['imagen']);

      // Agregar el item
      $this->cartHandler->addItem($item);
      
      Flasher::success(sprintf('Producto <b>%s</b> agregado al carrito de compras con éxito.', $producto['nombre']));
      Redirect::back('carrito');
      
    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back('carrito');
    }
  }

  function remover($itemId = null)
  {
    try {
      // Validar que exista el producto en la base de datos
      $found = true;
      if (!$product = productoModel::by_id($itemId)) {
        $found = false; // no existe en la base de datos
        Flasher::error('No existe el producto o no está disponible ya en la tienda.');
      }

      // Inicializar el carrito de compras
      $cart        = $this->cartHandler->loadCart();
      $items       = $cart['items'];

      // Verificar items en el carrito
      if (empty($items)) {
        throw new Exception('No hay productos en tu carrito.');
      }
      
      // Remover del carrito de compras
      if ($this->cartHandler->removeItem($itemId) === false) {
        throw new Exception('Hubo un problema al remover el producto de tu carrito.');
      }

      Flasher::success(sprintf('Producto <b>%s</b> removido del carrito de compras con éxito.', $found ? $product['nombre'] : 'no existente'));
      Redirect::to('carrito');
      
    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::to('carrito');
    }
  }

  function vaciar()
  {
    try {
      // Vaciar los items del carrito de compras
      $this->cartHandler->emptyCart();
      
      Flasher::success('Tu carrito de compras ha sido vaciado con éxito.');
      Redirect::to('carrito');
      
    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back('carrito');
    }
  }
}