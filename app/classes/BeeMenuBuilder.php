<?php 

final class BeeMenuBuilder
{

  private $menu;
  private $menuId;
  private $menuWrapper;
  private $menuClasses;
  private $menuActiveClass;
  private $items = [];
  private $currentSlug;

//   <li class="nav-item">
//   <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true"
//     aria-controls="collapseTwo">
//     <i class="fas fa-fw fa-cog"></i>
//     <span>Componentes</span>
//   </a>
//   <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
//     <div class="bg-white py-2 collapse-inner rounded">
//       <h6 class="collapse-header">SB Admin 2</h6>
//       <a class="collapse-item" href="admin/botones">Botones</a>
//       <a class="collapse-item" href="admin/cartas">Cartas</a>
//     </div>
//   </div>
// </li>

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
  }

  function getMenu()
  {
    $this->createMenu();

    return $this->menu;
  }
  
}
