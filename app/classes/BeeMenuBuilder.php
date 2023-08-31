<?php 
// TODO: Documentar la clase

final class BeeMenuBuilder
{
  private $menu;
  private $menuId;
  private $menuWrapper;
  private $menuClasses;
  private $menuActiveClass;
  private $items = [];
  private $currentSlug;

  function __construct($wrapperId = null, $wrapperTag = null, $wrapperClasses = null, $activeClass = null)
  {
    $this->menuId           = $wrapperId === null ? '' : $wrapperId;
    $this->menuWrapper      = $wrapperTag === null ? 'ul' : $wrapperTag;
    $this->menuClasses      = $wrapperClasses === null ? '' : $wrapperClasses;
    $this->menuActiveClass  = $activeClass === null ? 'active' : $activeClass;
  }

  function setCurrentSlug($slug)
  {
    $this->currentSlug = $slug;
  }

  function addItem(BeeMenuItem $item)
  {
    // Esta función deberá ser recursiva
    $this->items[] = $item->getItem();
  }

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
      $output .= sprintf(
        '<li id="%s" class="%s %s">
          <a class="%s" href="%s">
            %s
            <span>%s</span>
          </a>
        </li>',
        $item['id'],
        $item['class'],
        $item['slug'] === $this->currentSlug ? $this->menuActiveClass : '',
        $item['class'],
        $item['url'],
        $item['icon'],
        $item['text']
      );
    }

    $output .= sprintf(
      '</%s>', 
      $this->menuWrapper
    );

    $this->menu = $output;
    
    return $this->menu;
  }

  function getMenu()
  {
    return $this->createMenu();
  }
  
}
