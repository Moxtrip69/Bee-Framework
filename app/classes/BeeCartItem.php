<?php 

class BeeCartItem
{
  /**
   * ID del item
   *
   * @var mixed
   */
  public $id                 = null;

  /**
   * Nombre del producto
   *
   * @var string
   */
  public string $name        = '';

  /**
   * Precio del producto
   *
   * @var float
   */
  public float $price        = 0;

  /**
   * Descripción del producto
   *
   * @var string
   */
  public string $description = '';

  /**
   * Imagen del producto
   *
   * @var string
   */
  public string $image       = '';

  /**
   * Cantidad del producto
   *
   * @var integer
   */
  public int $quantity       = 1;

  /**
   * Si sólo puede adquirirse 1 unidad por carrito
   *
   * @var boolean
   */
  public bool $exclusive     = false;

  function __construct($id, $name, $price, $quantity = 1, $description = '', $image = '', $exclusive = false)
  {
    $this->id          = $id;
    $this->name        = $name;
    $this->price       = (float) $price;
    $this->quantity    = (int) $quantity;
    $this->description = $description;
    $this->image       = $image;
    $this->exclusive   = (bool) $exclusive;
  }

  /**
   * Regresa el item formateado para ser utilizado
   *
   * @return array
   */
  function getItem()
  {
    $item =
    [
      'id'          => $this->id,
      'name'        => $this->name,
      'price'       => $this->price,
      'description' => $this->description,
      'image'       => $this->image,
      'quantity'    => $this->quantity,
      'exclusive'   => $this->exclusive
    ];

    return $item;
  }
}

