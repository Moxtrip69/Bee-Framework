<?php

class BeeTableColumn
{
  protected $name;
  protected $label;
  protected $attributes = [];

  function __construct(string $name, string $label)
  {
    $this->name  = $name;
    $this->label = $label;
  }

  public function getLabel(): string
  {
    return $this->label;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setAttributes(array $attributes): self
  {
    $this->attributes = $attributes;

    return $this;
  }

  private function renderAttributes(array $attributes): string
  {
    $html = '';
    foreach ($attributes as $key => $value) {
      $html .= " {$key}='" . htmlspecialchars((string)$value, ENT_QUOTES) . "'";
    }
    
    return $html;
  }
}