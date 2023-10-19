<?php 

class Bs5Slider
{
  private $id           = 'Bs5Slider';
  private $classes      = '';
  private $interval     = 5000;
  private $transition   = 'slide';
  private $showControls = true;
  private $autoplay     = true;
  private $images       = [];

  public function setId(string $id)
  {
    $this->id = $id;  
  }

  function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  function setInterval(int $interval)
  {
    $this->interval = $interval;
  }

  function setTransition(string $transition)
  {
    $this->transition = $transition;
  }

  function setShowControls(bool $showControls)
  {
    $this->showControls = $showControls;
  }

  function setAutoplay(bool $autoplay)
  {
    $this->autoplay = $autoplay;
  }

  function setImages(array $images)
  {
    $this->images = $images;
  }

  function render()
  {
    // Contenedor wrapper del slider
    $html = '<div
    id="' . $this->id . '"
    class="carousel slide ' . ($this->transition === 'fade' ? 'carousel-fade' : '') . '"
    data-bs-ride="' . ($this->autoplay ? 'carousel' : 'true') . '"
    data-bs-interval="' . $this->interval . '"
    >';

    // Contenedor interno con las slides
    $html .= '<div class="carousel-inner ' . $this->classes . '">';
    $active = true;

    // Iterar e insertar las imágenes o slides
    foreach ($this->images as $image) {
      $html .= '<div class="carousel-item ' . ($active ? 'active' : '') . '">
      <img src="' . $image . '" class="d-block w-100" alt="Slider Imagen">
      </div>';
      $active = false;
    }

    $html .= '</div>';

    // Controles de navegación
    if ($this->showControls) {
      $html .= '<button class="carousel-control-prev" type="button" data-bs-target="#' . $this->id . '" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#' . $this->id . '" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>';
    }

    $html .= '</div>';
    return $html;
  }
}