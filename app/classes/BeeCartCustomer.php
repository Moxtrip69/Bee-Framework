<?php 

final class BeeCartCustomer
{
  private $customer;
  private $firstName;
  private $lastName;
  private $name;
  private $email;
  private $phone;
  private $address;
  private $line1;
  private $line2;
  private $city;
  private $state;
  private $country;
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
