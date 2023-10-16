<?php

class Bs5Slider
{
  /**
   * ID del slider, debe ser único
   *
   * @var string
   */
  private $id           = 'Bs5Slider';

  /**
   * Clases adicionales a aplicar
   *
   * @var string
   */
  private $classes      = '';

  /**
   * Intervalo en milisegundos entre cada slide
   *
   * @var integer
   */
  private $interval     = 5000;

  /**
   * Tipo de transición, fade o slide
   *
   * @var string
   */
  private $transition   = 'slide';

  /**
   * Mostrar los controles de navegación manual
   *
   * @var boolean
   */
  private $showControls = true;

  /**
   * Autoiniciar el slider o no
   *
   * @var boolean
   */
  private $autoplay     = true;

  /**
   * Array de imágenes que serán mostradas en el carrusel o slider
   *
   * @var array
   */
  private $images       = [];


  public function setId(string $id)
  {
    $this->id = $id;
  }

  public function setClasses(string $classes)
  {
    $this->classes = $classes;
  }

  public function setInterval(int $interval)
  {
    $this->interval = $interval;
  }

  public function setControls(bool $showControls)
  {
    $this->showControls = $showControls;
  }

  public function setTransition(string $transition)
  {
    $this->transition = $transition;
  }

  public function setAutoplay(bool $autoplay)
  {
    $this->autoplay = $autoplay;
  }

  public function setImages(array $images)
  {
    $this->images = $images;
  }

  public function render()
  {
    $html   = '<div 
    id="' . $this->id . '" 
    class="carousel slide ' . ($this->transition === 'fade' ? 'carousel-fade' : '') . '" 
    data-bs-ride="' . ($this->autoplay ? 'carousel' : 'true') . '" 
    data-bs-interval="' . $this->interval . '">';

    $html  .= '<div class="carousel-inner ' . $this->classes . '">';
    $active = true;

    foreach ($this->images as $image) {
      $html .= '<div class="carousel-item';
      if ($active) {
        $html .= ' active';
        $active = false;
      }
      $html .= '">';
      $html .= '<img src="' . $image . '" class="d-block w-100" alt="Slider Image">';
      $html .= '</div>';
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