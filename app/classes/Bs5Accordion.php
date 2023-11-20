<?php 

/**
 * Esta clase es sólo educativa, no aporta nada al Framework, puede ser borrada
 */
class Bs5Accordion
{
  private $id      = 'Bs5Accordion';
  private $classes = '';
  private $items   = [];
  
  function setId(string $id)
  {
    $this->id = $id;
  }

  function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  function addItem(string $title, string $content, bool $collapsed = true)
  {
    $item = [
      'title'     => $title,
      'content'   => $content,
      'collapsed' => $collapsed
    ];

    $this->items[] = $item;
  }

  function render()
  {
    $html = sprintf('<div class="accordion accordion-flush %s" id="%s">', $this->classes, $this->id);

    foreach ($this->items as $key => $item) {
      $html .= 
      '<div class="accordion-item">
        <h2 class="accordion-header" id="heading' . $key . '">
          <button class="accordion-button ' . ($item['collapsed'] ? 'collapsed' : '') . '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $key . '" aria-expanded="' . ($item['collapsed'] ? 'false' : 'true') . '" aria-controls="collapse' . $key . '">
            ' . $item['title'] . '
          </button>
        </h2>

        <div id="collapse' . $key . '" class="accordion-collapse collapse' . ($item['collapsed'] ? '' : 'show') . '" data-bs-parent="#' . $this->id . '">
          <div class="accordion-body">' . $item['content'] . '</div>
        </div>
      </div>';
    }

    $html .= '</div>';

    return $html;
  }
}


