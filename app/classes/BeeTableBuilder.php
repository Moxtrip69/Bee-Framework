<?php

class BeeTableBuilder
{
  private array $attributes = [];
  private array $columns    = [];
  private ?array $data      = null;
  private ?string $asyncUrl = null;
  private string $id;

  public function __construct(?array $data = null, string $id = 'table_1', array $attributes = [])
  {
    $this->data       = $data;
    $this->id         = $id;
    $this->attributes = $attributes;
  }

  public function setAttributes(array $attributes): self
  {
    $this->attributes = $attributes;
    return $this;
  }

  public function addColumn(BeeTableColumn $column): self
  {
    $this->columns[] = $column;
    return $this;
  }

  public function setAsync(string $url): self
  {
    $this->asyncUrl = $url;
    return $this;
  }

  public function render(): string
  {
    if ($this->asyncUrl) {
      return $this->renderAsyncWrapper();
    }

    return $this->renderTable();
  }

  private function renderTable(): string
  {
    $attr  = $this->renderAttributes($this->attributes);
    $thead = "<tr>";
    foreach ($this->columns as $col) {
      $thead .= "<th>{$col->getLabel()}</th>";
    }
    $thead .= "</tr>";

    $tbody = "";
    if ($this->data) {
      foreach ($this->data as $row) {
        $tbody .= "<tr>";
        foreach ($this->columns as $col) {
          $tbody .= "<td>{$col->render($row)}</td>";
        }
        $tbody .= "</tr>";
      }
    }

    return "
      <table id='{$this->id}' {$attr}>
        <thead>{$thead}</thead>
        <tbody>{$tbody}</tbody>
      </table>
    ";
  }

  private function renderAsyncWrapper(): string
  {
    return "
      <div id='{$this->id}_wrapper'>Cargando...</div>
      <script>
        async function loadTable_{$this->id}() {
          const res = await fetch('{$this->asyncUrl}');
          const html = await res.text();
          document.getElementById('{$this->id}_wrapper').innerHTML = html;
        }
          
        loadTable_{$this->id}();
      </script>
    ";
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
