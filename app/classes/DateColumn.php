<?php 

class DateColumn extends BeeTableColumn
{
  private string $format;

  public function __construct(string $name, string $label, string $format = 'd/m/Y H:i')
  {
    parent::__construct($name, $label);
    $this->format = $format;
  }

  public function render($row): string
  {
    $value = $row[$this->name] ?? null;
    if (!$value) return '';
    $date = new DateTime($value);
    return $date->format($this->format);
  }
}