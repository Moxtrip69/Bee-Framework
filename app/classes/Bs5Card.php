<?php

class Bs5Card
{
  private $id            = 'Bs5Card';
  private $header        = '';
  private $headerButtons = [];
  private $title         = '';
  private $body          = '';
  private $footer        = '';
  private $image         = '';

  function setId(string $id)
  {
    $this->id = $id;
  }

  public function setHeader(string $header)
  {
    $this->header = $header;
  }

  function setTitle(string $title)
  {
    $this->title = $title;
  }

  public function addHeaderButton(string $text, string $url, string $class = '')
  {
    $this->headerButtons[] = '<a href="' . $url . '" class="btn btn-sm ' . $class . '">' . $text . '</a>';
  }

  public function setBody(string $body)
  {
    $this->body = $body;
  }

  public function setFooter(string $footer)
  {
    $this->footer = $footer;
  }

  public function setImage(string $image)
  {
    $this->image = $image;
  }

  public function render()
  {
    $html = '<div class="card" id="' . $this->id . '">';

    if (!empty($this->image)) {
      $html .= '<img src="' . $this->image . '" class="card-img-top" alt="Card Image">';
    }

    if (!empty($this->header)) {
      $html .= '<div class="card-header d-flex justify-content-between align-items-center">' . $this->header;

      // Agregar botones al encabezado
      if (!empty($this->headerButtons)) {
        $html .= '<div class="btn-group">';
        $html .= implode('', $this->headerButtons);
        $html .= '</div>';
      }

      $html .= '</div>';
    }

    if (!empty($this->body)) {
      $html .= '<div class="card-body">';
      $html .= !empty($this->title) ? sprintf('<h5 class="card-title">%s</h5>', $this->title) : '';
      $html .= $this->body;
      $html .= '</div>';
    }

    if (!empty($this->footer)) {
      $html .= '<div class="card-footer">' . $this->footer . '</div>';
    }

    $html .= '</div>';
    return $html;
  }
}
