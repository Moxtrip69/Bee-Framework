<?php 

class BeeCartItem
{
  public $id                 = null;
  public string $name        = '';
  public float $price        = 0;
  public string $description = '';
  public string $image       = '';
  public int $quantity       = 1;
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

