<?php 

class BeeCartShipping
{
  /**
   * El objeto shipping en si, lo que será incluido en el carrito
   *
   * @var array
   */
  private $shipping;

  /**
   * La paquetería que será utilizada
   *
   * @var string
   */
  private $courier;

  /**
   * El precio del servicio
   *
   * @var float
   */
  private $price;

  /**
   * La fecha estimada de entrega en segundos
   *
   * @var int
   */
  private $deadline;

  /**
   * Constructor para agregar un shipping al carrito
   *
   * @param string $courier Paquetería utilizada ej. Fedex, DHL, Redpack, 99minutos
   * @param float $price Precio del servicio
   * @param integer $deadline Fecha de entrega estimada expresada en segundos
   */
  function __construct(string $courier, float $price, int $deadline)
  {
    $this->courier  = $courier;
    $this->deadline = $deadline;
    $this->price    = $price;
  }

  /**
   * Regresa el shipping formateado listo para ser utilizado
   *
   * @return void
   */
  function getShipping()
  {
    $this->shipping =
    [
      'courier'  => $this->courier,
      'price'    => $this->price,
      'deadline' => $this->deadline,
      'date'     => date('Y-m-d', $this->deadline)
    ];

    return $this->shipping;
  }
}
