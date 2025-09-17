<?php 

class BadgeColumn extends BeeTableColumn
{
  private array $map;

  public function __construct(string $name, string $label, array $map = [])
  {
    parent::__construct($name, $label);
    $this->map = $map;
  }

  public function render($row): string
  {
    $value = $row[$this->name] ?? '';
    $color = $this->map[$value] ?? 'gray';
    return sprintf(
      '<span class="badge badge-%s">%s</span>',
      htmlspecialchars($color),
      htmlspecialchars(ucfirst($value))
    );
  }
}