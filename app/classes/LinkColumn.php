<?php 

class LinkColumn extends BeeTableColumn
{
  private string $urlField;

  public function __construct(string $name, string $label, string $urlField)
  {
    parent::__construct($name, $label);
    $this->urlField = $urlField;
  }

  public function render($row): string
  {
    $text = htmlspecialchars($row[$this->name] ?? '');
    $url  = htmlspecialchars($row[$this->urlField] ?? '#');
    return sprintf('<a href="%s" target="_blank">%s</a>', $url, $text);
  }
}