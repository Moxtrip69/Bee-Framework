<?php 

/**
 * Un link dentro de un item de navegación
 */
class BeeMenuItemLink
{
  /**
   * El objeto del link
   *
   * @var array
   */
  protected $link     = [];

  /**
   * Icono en su representación HTML5 completa
   *
   * @var string
   */
  protected $icon     = '';

  /**
   * El texto del enlace
   *
   * @var string
   */
  protected $text     = '';

  /**
   * El URL del enlace
   *
   * @var string
   */
  protected $href     = '';
  
  /**
   * Clases del enlace
   *
   * @var string
   */
  protected $classes  = '';

  /**
   * ID del enlace
   *
   * @var string
   */
  protected $id       = '';

  /**
   * Subelementos del enlace
   *
   * @var array
   */
  protected $subItems = [];

  function __construct()
  {
  }

  /**
   * El texto contenido en el link
   *
   * @param string $text
   * @return void
   */
  function setText(string $text)
  {
    $this->text = $text;
  }

  /**
   * El URL del atributo href del link
   *
   * @param string $url
   * @return void
   */
  function setUrl(string $url)
  {
    $this->href = $url;
  }

  /**
   * El ID del link
   *
   * @param string $id
   * @return void
   */
  function setId(string $id)
  {
    $this->id = $id;
  }

  /**
   * El icono del link
   *
   * @param string $icon
   * @return void
   */
  function setIcon(string $icon)
  {
    $this->icon = $icon;
  }

  /**
   * Las clases del link
   *
   * @param string $classes
   * @return void
   */
  function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  /**
   * Establecer un subelemento del enlace
   *
   * @param BeeMenuItem $item
   * @return void
   */
  function setSubItem(BeeMenuItem $item)
  {
    $this->subItems[] = $item->getItem();
  }

  /**
   * Procesar y formatear el array del link final
   *
   * @return void
   */
  private function process()
  {
    $this->link =
    [
      'icon'     => $this->icon,
      'text'     => $this->text,
      'href'     => $this->href,
      'classes'  => $this->classes,
      'id'       => $this->id,
      'subItems' => $this->subItems
    ];

    return $this->link;
  }

  /**
   * Regresa el link completo y su array de información
   *
   * @return array
   */
  function getLink()
  {
    return $this->process();
  }

  /**
   * Construye un enlace individual html5
   *
   * @return void
   */
  function buildLink()
  {
    return sprintf(
      '<a href="%s" id="%s" class="%s%s">%s%s</a>',
      $this->href,
      $this->id,
      $this->classes,
      $this->icon,
      $this->text
    );
  }
}