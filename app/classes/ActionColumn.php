<?php

class ActionColumn extends BeeTableColumn
{
  private array $actions;

  public function __construct(string $label, array $actions)
  {
    parent::__construct('__actions', $label);
    $this->actions = $actions;
  }

  public function renderCell(array $row): string
  {
    $html = '';
    foreach ($this->actions as $action) {
      $url = $action['url'] . '?id=' . urlencode((string)($row['id'] ?? ''));
      $html .= "<a href='{$url}' class='{$action['class']}'>{$action['label']}</a> ";
    }
    return $html;
  }
}
