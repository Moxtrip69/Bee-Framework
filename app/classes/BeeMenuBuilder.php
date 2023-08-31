<?php 

class BeeMenuBuilder
{
  /**
   * El menú final
   *
   * @var array
   */
  private $menu            = [];

  /**
   * El ID del menú
   *
   * @var string
   */
  private $menuId          = '';

  /**
   * La etiqueta html5 que se usará para envolver al menú
   *
   * @var string
   */
  private $menuWrapper     = 'ul';

  /**
   * Las clases del menú
   *
   * @var string
   */
  private $menuClasses     = '';

  /**
   * La clase para elementos activos
   *
   * @var string
   */
  private $menuActiveClass = 'active';

  /**
   * Todos los items o elementos del menú
   *
   * @var array
   */
  private $items           = [];

  /**
   * Slug actual a utilizar para determinar que elemento activar
   *
   * @var string
   */
  private $currentSlug     = '';

  function __construct($wrapperId = null, $wrapperTag = null, $wrapperClasses = null, $activeClass = null)
  {
    $this->menuId           = $wrapperId === null ? $this->menuId : $wrapperId;
    $this->menuWrapper      = $wrapperTag === null ? $this->menuWrapper : $wrapperTag;
    $this->menuClasses      = $wrapperClasses === null ? $this->menuClasses : $wrapperClasses;
    $this->menuActiveClass  = $activeClass === null ? $this->menuActiveClass : $activeClass;
  }

  /**
   * Establece el slug actual para determinar que item activar
   *
   * @param string $slug
   * @return void
   */
  function setCurrentSlug(string $slug)
  {
    $this->currentSlug = $slug;
  }

  /**
   * Agrega un item al menú
   *
   * @param BeeMenuItem $item
   * @return void
   */
  function addItem(BeeMenuItem $item)
  {
    // Esta función deberá ser recursiva
    $this->items[] = $item->getItem();
  }

  /**
   * Agrega una serie de items al menú
   *
   * @param array $items
   * @return void
   */
  function addItems(array $items)
  {
    foreach ($items as $item) {
      $this->items[] = $item->getItem();
    }
  }

  /**
   * Construye el menú completamente usando todos los elementos
   *
   * @return void
   */
  private function createMenu()
  {
    $output = sprintf(
      '<%s id="%s" class="%s">', 
      $this->menuWrapper, 
      $this->menuId, 
      $this->menuClasses
    );

    foreach ($this->items as $item) {
      // Si no tiene submenús
      $link    = $item['link'];
      $output .= sprintf(
        '<li id="%s" class="%s">
          <a id="%s" class="%s %s" href="%s">%s%s</a>
        </li>',
        $item['id'],
        $item['classes'],
        $link['id'],
        $link['classes'],
        $item['slug'] === $this->currentSlug ? $this->menuActiveClass : '',
        $link['href'],
        $link['icon'],
        $link['text']
      );
    }

    $output .= sprintf(
      '</%s>', 
      $this->menuWrapper
    );

    $this->menu = $output;
    
    return $this->menu;
  }

  /**
   * Regresa el menú formateado y en HTML5 completo
   *
   * @return void
   */
  function getMenu()
  {
    return $this->createMenu();
  }
  
}
