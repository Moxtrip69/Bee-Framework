<?php 

class BeeCartCoupon 
{
  /**
   * El objeto cupón en si, será lo que se añada al carrito
   *
   * @var array
   */
  private $coupon;

  /**
   * Código de descuento, será visible para el cliente
   *
   * @var string
   */
  private $code;

  /**
   * La descripción del cupón, será visible para el cliente
   *
   * @var string
   */
  private $description;

  /**
   * Es el descuento que el cupón otorga
   *
   * @var float
   */
  private $discount;

  /**
   * Es el tipo de descuento que puede ser aplicado, flat o percent
   *
   * @var string
   */
  private $type;

  /**
   * La fecha máxima de uso del cupón expresada en segundos
   *
   * @var int
   */
  private $deadline;

  /**
   * Objeto de cupón para el carrito de compras
   *
   * @param string $code es el código que deberá usar el cliente para recibir el descuento
   * @param string $description descripción visible para el cliente sobre el descuento
   * @param float $discount descuento que recibirá el cliente
   * @param string $type tipo de descuento, puedes establecer tus propias reglas en tu lógica, ej. flat para descuento de una cifra plana, o percentaje para descuento por porcentaje
   * @param integer $deadline la fecha máxima de uso expresada en segundos
   */
  function __construct(string $code, string $description, float $discount, string $type, int $deadline)
  {
    $this->code        = $code;
    $this->description = $description;
    $this->discount    = $discount;
    $this->type        = $type;
    $this->deadline    = $deadline;
  }

  /**
   * Regresa el array con toda la información del cupón para ser utilizada en el carrito
   *
   * @return array
   */
  function getCoupon()
  {
    $this->coupon =
    [
      'code'        => $this->code,
      'description' => $this->description,
      'discount'    => (float) $this->discount,
      'type'        => $this->type,
      'deadline'    => $this->deadline,
      'date'        => date('Y-m-d H:i:s', $this->deadline)
    ];

    return $this->coupon;
  }
}
