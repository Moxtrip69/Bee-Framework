<?php 

/**
 * Esta clase es sólo educativa, no aporta nada al Framework, puede ser borrada
 */
class Bs5Card
{
  private $id            = 'Bs5Card';
  private $classes       = '';
  private $image         = '';

  private $header        = '';
  private $headerButtons = [];

  private $title         = '';
  private $body          = '';
  private $footer        = '';

  function setId(string $id)
  {
    $this->id = $id;
  }

  function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  function setImage(string $image)
  {
    $this->image = $image;
  }

  function setHeader(string $header)
  {
    $this->header = $header;
  }

  function addHeaderButton(string $text, string $url, string $class = '')
  {
    $this->headerButtons[] = sprintf('<a href="%s" class="btn btn-sm %s">%s</a>', $url, $class, $text);
  }

  function setTitle(string $title)
  {
    $this->title = $title;
  }

  function setBody(string $body)
  {
    $this->body = $body;
  }

  function setFooter(string $footer)
  {
    $this->footer = $footer;
  }

  function render()
  {
    // Wrapper general de la carta
    $html = sprintf('<div class="card %s" id="%s">', $this->classes, $this->id);

    // Imagen de la carta
    if (!empty($this->image)) {
      $html .= sprintf('<img src="%s" class="card-img-top" alt="Imagen de la carta %s">', $this->image, $this->id);
    }

    // Header de la carta
    if (!empty($this->header)) {
      $html .= '<div class="card-header d-flex justify-content-between align-items-center">';
      $html .= $this->header;

      // Si hay botones, los anexamos
      if (!empty($this->headerButtons)) {
        $html .= '<div class="btn-group">';
        $html .= implode('', $this->headerButtons);
        $html .= '</div>';
      }


      $html .= '</div>';
    }

    // Contenido de la carta
    if (!empty($this->body)) {
      $html .= '<div class="card-body">';
      $html .= !empty($this->title) ? sprintf('<h5 class="card-title">%s</h5>', $this->title) :'';
      $html .= $this->body;
      $html .= '</div>';
    }

    // Footer de la carta
    if (!empty($this->footer)) {
      $html .= sprintf('<div class="card-footer">%s</div>', $this->footer);
    }

    $html .= '</div>';
    return $html;
  }
}

// <div class="card" style="width: 18rem;">
//   <img src="..." class="card-img-top" alt="...">
//   <div class="card-body">
//     <h5 class="card-title">Card title</h5>
//     <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
//     <a href="#" class="btn btn-primary">Go somewhere</a>
//   </div>
// </div>
