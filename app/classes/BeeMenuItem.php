<?php 

class BeeMenuItem
{
  /**
   * El item completo y su información
   *
   * @var array
   */
  private $item    = [];

  /**
   * El elemento link
   *
   * @var array
   */
  private $link    = [];

  /**
   * Las clases del item
   *
   * @var string
   */
  private $classes = '';

  /**
   * El ID del item
   *
   * @var string
   */
  private $id      = '';

  /**
   * El slug del item
   *
   * @var string
   */
  private $slug    = '';

  function __construct()
  {
  }

  /**
   * Establace el link que contendrá el item
   *
   * @param BeeMenuItemLink $link
   * @return void
   */
  function setLink(BeeMenuItemLink $link)
  {
    $this->link = $link->getLink();
  }

  /**
   * Establece las clases del item
   *
   * @param string $classes
   * @return void
   */
  function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  /**
   * Establece el ID del item
   *
   * @param string $id
   * @return void
   */
  function setId(string $id)
  {
    $this->id = $id;  
  }

  /**
   * Establece el slug para determinar si el item será activo o no
   *
   * @param string $slug
   * @return void
   */
  function setSlug(string $slug)
  {
    $this->slug = $slug;
  }

  /**
   * Procesa y formatea el item
   *
   * @return void
   */
  private function process()
  {
    $this->item =
    [
      'id'      => $this->id,
      'classes' => $this->classes,
      'slug'    => $this->slug,
      'link'    => $this->link
    ];

    return $this->item;
  }

  /**
   * Regresa el item formateado y sus elementos
   *
   * @return array
   */
  function getItem()
  {
    return $this->process();
  }
}
