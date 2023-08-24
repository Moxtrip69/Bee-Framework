<?php 

final class BeeCartHandler
{
  /**
   * Es el nombre de la clave dentro de las variables de sesión
   *
   * @var string
   */
  public $sessionName = 'checkout-cart';
  
  /**
   * El carrito en si, todo su contenido y propiedades
   *
   * @var array
   */
  private $cart;

  /**
   * El identificador único del carrito, un token no repetible
   *
   * @var string
   */
  private $id;

  /**
   * Todos los items o productos dentro del carrito de compras
   *
   * @var array
   */
  private $items      = [];

  /**
   * Representación númerica del total de productos agregados en el carrito
   *
   * @var integer
   */
  private $totalItems = 0;

  /**
   * La divisa por defecto del carrito
   *
   * @var string
   */
  private $currency   = 'MXN';

  /**
   * El subtotal de los items en el carrito antes de impuestos y descuentos
   *
   * @var float
   */
  private $subtotal   = 0;

  /**
   * El total de impuestos cobrados
   *
   * @var float
   */
  private $taxes      = 0;

  /**
   * Representa el porcentaje de impuestos a cobrar
   * base es 16 para el 16% en México como IVA
   *
   * @var float
   */
  private $taxesRate  = 16;

  /**
   * Información del envío
   * 
   * BeeCartShipping
   *
   * @var array
   */
  private $shipping   = [];

  /**
   * Todos los descuentos aplicables al carrito
   *
   * @var float
   */
  private $discounts   = 0;

  /**
   * Cupón de descuento aplicado en el carrito
   *
   * @var array
   */
  private $coupon      = [];
  
  /**
   * Monto total a pagar con impuestos, descuentos y envío
   *
   * @var float
   */
  private $total       = 0;

  /**
   * Sufijo a ser utilizado por el sistema en sus órdenes o ventas
   *
   * @var string
   */
  private $suffix      = 'ORD';

  /**
   * Número de compra o venta
   *
   * @var mixed
   */
  private $orderId;

  /**
   * Extracto que aparecerá en el resumen de compra y pasarelas de pago
   *
   * @var string
   */
  private $description;

  /**
   * Toda la información del cliente nombre, email, dirección
   *
   * @var array
   */
  private $customer    = [];

  function __construct($keyName = null)
  {
    $this->sessionName = $keyName !== null ? $keyName : $this->sessionName; // es el nombre de la clave dentro de $_SESSION[]

    if (!isset($_SESSION[$this->sessionName])) {
      $this->cart = $_SESSION[$this->sessionName] = $this->newCart();
    } else {
      $this->cart = $_SESSION[$this->sessionName];
    }
  }

  /**
   * Regresa una estructura nueva del carrito de compras
   *
   * @return array
   */
  private function newCart()
  {
    // Estructura general del carrito
    $cart =
    [
      'id'              => generate_key(),
      'orderId'         => random_password(8, 'numeric'),
      'suffix'          => $this->suffix,
      'description'     => $this->description,
      'currency'        => $this->currency,
      'subtotal'        => $this->subtotal,
      'taxes'           => $this->taxes,
      'rate'            => $this->taxesRate,
      'shipping'        => $this->shipping,
      'discounts'       => $this->discounts,
      'total'           => $this->total,
      'coupon'          => $this->coupon,
      'customer'        => $this->customer,
      'totalItems'      => $this->totalItems,
      'items'           => $this->items
    ];

    return $cart;
  }

  /**
   * Cargar información general del carrito de compras
   * Al usar este método no se realizan operaciones al ser cargado
   *
   * @return array
   */
  private function getCart()
  {
    if (!isset($_SESSION[$this->sessionName])) {
      return $_SESSION[$this->sessionName] = $this->newCart();
    }

    return $_SESSION[$this->sessionName];
  }

  /**
   * Cargar el carrito de compras completo y procesado
   *
   * @return array
   */
  function loadCart()
  {
    return $this->recalculateCart();
  }

  static function get()
  {
    $cart = new Self();
    return $cart->loadCart();
  }

  /**
   * Cargar todos los items agregados en el carrito de compras
   *
   * @return array
   */
  private function getItems()
  {
    return $_SESSION[$this->sessionName]['items'];
  }

  // Cargar un item del carrito

  /**
   * Agregar un producto al carrito o actualizar su cantidad en caso de que ya exista
   *
   * @param BeeCartItem $item
   * @return bool
   */
  function addItem(BeeCartItem $item)
  {
    $this->items = $this->getItems();

    // Iterar sobre todos los productos en el carrito
    foreach ($this->items as $i => $currentItem) {
      if ($item->id == $currentItem['id']) {
        $this->items[$i]['quantity'] += $item->exclusive === true ? 0 : $item->quantity;

        // Actualizar en sesión
        $_SESSION[$this->sessionName]['items'] = $this->items;
        return true;
      }
    }

    // No existe en los productos del carrito, se anexa
    $item          = $item->getItem();

    // Se añade el item al resto de items
    $this->items[] = $item;

    // Actualizar en sesión
    $_SESSION[$this->sessionName]['items'] = $this->items;
    return true;
  }

  /**
   * Elimina o quita un item del carrito actual
   *
   * @param mixed $itemId
   * @return bool
   */
  function removeItem($itemId)
  {
    $this->items = $this->getItems();

    // Iterar sobre todos los productos en el carrito
    foreach ($this->items as $i => $currentItem) {
      if ($itemId == $currentItem['id']) {
        unset($this->items[$i]);

        // Actualizar en sesión
        $_SESSION[$this->sessionName]['items'] = $this->items;
        return true;
      }
    }

    // No existe en los productos del carrito
    return false;
  }

  /**
   * Agregar la información del customer o cliente al objeto del carrito
   *
   * @param BeeCartCustomer $customer
   * @return void
   */
  function addCustomer(BeeCartCustomer $customer)
  {
    $_SESSION[$this->sessionName]['customer'] = $customer->getCustomer();
  }

  /**
   * Agregar un shipping o envío al carrito de compras
   *
   * @param BeeCartShipping $shipping
   * @return void
   */
  function addShipping(BeeCartShipping $shipping)
  {
    $_SESSION[$this->sessionName]['shipping'] = $shipping->getShipping();
  }

  /**
   * Agregar un cupón o coupon al carrito de compras
   *
   * @param BeeCartCoupon $coupon
   * @return void
   */
  function addCoupon(BeeCartCoupon $coupon)
  {
    $_SESSION[$this->sessionName]['coupon'] = $coupon->getCoupon();
  }

  /**
   * Vaciar el contenido de los items del carrito de compras
   *
   * @return void
   */
  function emptyCart()
  {
    $_SESSION[$this->sessionName]['items'] = [];
  }

  /**
   * Reinicia el carrito de compras actual del usuario
   *
   * @return void
   */
  function restartCart()
  {
    unset($_SESSION[$this->sessionName]);
    $_SESSION[$this->sessionName] = $this->newCart();
  }

  /**
   * Realiza todas las operaciones requeridas para obtener la información final
   * de montos y totales del contenido
   *
   * @return array
   */
  private function recalculateCart()
  {
    $this->cart        = $_SESSION[$this->sessionName]; // Se tiene que recargar en todo momento debido a que puede resultar la información desactualizada
    $this->orderId     = $this->cart['orderId'];
    $this->suffix      = $this->cart['suffix'];
    $this->items       = $this->cart['items'];
    $this->coupon      = $this->cart['coupon'];
    $this->shipping    = $this->cart['shipping'];
    $this->totalItems  = empty($this->items) ? 0 : count($this->items);
    $this->description = sprintf('%s%s - Sin productos en el carrito.', $this->suffix, $this->orderId);
    
    // Sólo si hay items iteramos sobre ellos
    if (!empty($this->items)) {
      $this->description = sprintf('%s%s - %s%s.', $this->suffix, $this->orderId, $this->items[0]['name'], $this->totalItems > 1 ? sprintf(' y %s más', $this->totalItems - 1) : '');

      foreach ($this->items as $item) {
        // Calcular el subtotal multiplicando quantity * price
        $subtotal        = $item['price'] * $item['quantity'];
        $this->subtotal += $subtotal;
      }

      // Realizar los cálculos finales
      $this->subtotal = round($this->subtotal / (($this->taxesRate / 100) + 1), 2);
      $this->taxes    = round($this->subtotal * ($this->taxesRate / 100), 2);

      
      // Calcular envío y sumarlo al monto total
      $shippingPrice  = !empty($this->shipping) ? $this->shipping['price'] : 0;

      // Monto neto total a pagar
      $this->total    = round($this->subtotal + $this->taxes + $shippingPrice, 2);

      // Calcular descuentos si existen en el carrito
      if (!empty($this->coupon)) {
        switch ($this->coupon['type']) {
          case 'flat':
            $this->discounts = $this->coupon['discount'];
            $this->total     = round((($this->subtotal + $this->taxes) - $this->discounts) + $shippingPrice, 2);
            break;
          
          case 'percentage':
            $preTotal        = round($this->subtotal + $this->taxes, 2);
            $this->discounts = $preTotal * ($this->coupon['discount'] / 100);
            $this->total     = round((($this->subtotal + $this->taxes) - $this->discounts) + $shippingPrice, 2);
            break;
        }
      }
    }

    // Array de información que se actualizará
    $update =
    [
      'totalItems'  => $this->totalItems,
      'description' => $this->description,
      'subtotal'    => $this->subtotal,
      'taxes'       => $this->taxes,
      'discounts'   => $this->discounts,
      'total'       => $this->total
    ];

    $this->cart = array_merge($this->cart, $update);

    // Actualizar en sesión
    $_SESSION[$this->sessionName] = $this->cart;

    return $this->cart;
  }

  /**
   * Regresa el ID de la orden o número de compra
   *
   * @return string
   */
  function getOrderId()
  {
    $this->cart    = $this->getCart();
    $this->orderId = $this->cart['orderId'];

    return $this->orderId;
  }

  /**
   * Regresa el ID del carrito de compras (el token único)
   *
   * @return string
   */
  function getCartId()
  {
    $this->cart = $this->getCart();
    $this->id   = $this->cart['id'];

    return $this->id;
  }
}
