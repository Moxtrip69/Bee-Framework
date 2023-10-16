<?php

class Bs5Accordion
{
  private $id    = 'Bs5Accordion';
  private $items = [];

  function setId(string $id)
  {
    $this->id = $id;
  }

  public function addItem($title, $content, $collapsed = true)
  {
    $item = [
      'title'     => $title,
      'content'   => $content,
      'collapsed' => $collapsed,
    ];

    $this->items[] = $item;
  }

  public function render()
  {
    $html = '<div class="accordion" id="' . $this->id . '">';
    foreach ($this->items as $key => $item) {
      $html .= '<div class="accordion-item">';
      $html .= '<h2 class="accordion-header" id="heading' . $key . '">';
      $html .= '<button class="accordion-button' . ($item['collapsed'] ? ' collapsed' : '') . '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $key . '" aria-expanded="' . (!$item['collapsed'] ? 'true' : 'false') . '" aria-controls="collapse' . $key . '">';
      $html .= $item['title'];
      $html .= '</button>';
      $html .= '</h2>';
      $html .= '<div id="collapse' . $key . '" class="accordion-collapse collapse' . ($item['collapsed'] ? '' : ' show') . '" aria-labelledby="heading' . $key . '" data-bs-parent="#' . $this->id . '">';
      $html .= '<div class="accordion-body">' . $item['content'] . '</div>';
      $html .= '</div>';
      $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
  }
}
