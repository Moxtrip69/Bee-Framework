<?php 

class BeeCartCustomer
{
  /**
   * El objeto del cliente completo
   *
   * @var array
   */
  private $customer;

  /**
   * Primer nombre del cliente
   *
   * @var string
   */
  private $firstName;

  /**
   * Apellidos del cliente
   *
   * @var string
   */
  private $lastName;

  /**
   * Nombre completo del cliente
   *
   * @var string
   */
  private $name;

  /**
   * Correo electrónico
   *
   * @var string
   */
  private $email;

  /**
   * Teléfono de contacto
   *
   * @var string
   */
  private $phone;

  /**
   * La dirección completa del cliente
   *
   * @var array
   */
  private $address;

  /**
   * Línea uno de la dirección y número
   *
   * @var string
   */
  private $line1;

  /**
   * Línea dos de la dirección y es opcional
   *
   * @var string
   */
  private $line2;

  /**
   * Ciudad o municipio del cliente
   *
   * @var string
   */
  private $city;

  /**
   * Estado del cliente
   *
   * @var string
   */
  private $state;

  /**
   * País del cliente
   *
   * @var string
   */
  private $country;

  /**
   * Código postal del cliente
   *
   * @var string
   */
  private $zp;

  function __construct($firstName, $lastName, $email, $phone = '')
  {
    $this->firstName = $firstName;
    $this->lastName  = $lastName;
    $this->name      = sprintf('%s %s', $this->firstName, $this->lastName);
    $this->email     = $email;
    $this->phone     = $phone;
  }

  function setLine1($line1)
  {
    $this->line1 = $line1;  
  }

  function setLine2($line2)
  {
    $this->line2 = $line2;  
  }

  function setCity($city)
  {
    $this->city = $city;  
  }

  function setState($state)
  {
    $this->state = $state;
  }

  function setCountry($country)
  {
    $this->country = $country;
  }

  /**
   * Establece el código postal o zip code
   *
   * @param string $zp
   * @return void
   */
  function setZp($zp)
  {
    $this->zp = $zp; // debería de requerir validación, esto deberá ser realizado ya por el desarrollador final
  }

  /**
   * Obtiene el objeto del customer listo para ser usado en el carrito
   *
   * @return array
   */
  function getCustomer()
  {
    $this->customer = 
    [
      'firstName' => $this->firstName,
      'lastName'  => $this->lastName,
      'name'      => $this->name,
      'email'     => $this->email,
      'phone'     => $this->phone,
    ];

    $this->address = 
    [
      'line1'     => $this->line1,
      'line2'     => $this->line2,
      'city'      => $this->city,
      'state'     => $this->state,
      'country'   => $this->country,
      'zp'        => $this->zp
    ];

    $this->customer = array_merge($this->customer, $this->address);

    return $this->customer;
  }
}
