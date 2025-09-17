<?php 

class ProgressColumn extends BeeTableColumn
{
  public function render($row): string
  {
    $value   = $row[$this->name] ?? 0;
    $percent = max(0, min(100, (int) $value)); // Asegurar 0–100

    return sprintf(
      '<div class="progress" style="background:#eee; border-radius:6px; overflow:hidden; width:100%%;">
        <div style="width:%d%%; background:#4caf50; color:#fff; text-align:center; font-size:12px; line-height:20px;">
          %d%%
        </div>
      </div>',
      $percent,
      $percent
    );
  }
}
