<?php

class TextColumn extends BeeTableColumn
{
  public function render(array $row): string
  {
    return htmlspecialchars((string)($row[$this->name] ?? ''), ENT_QUOTES);
  }
}
