<?php 

class ImageColumn extends BeeTableColumn
{
  private int $width;
  private int $height;

  public function __construct(string $name, string $label, int $width = 50, int $height = 50)
  {
    parent::__construct($name, $label);
    $this->width  = $width;
    $this->height = $height;
  }

  public function render($row): string
  {
    $src = htmlspecialchars($row[$this->name] ?? '');
    return $src
      ? sprintf(
        '<img src="%s" width="%d" height="%d" style="object-fit:cover; border-radius:4px;">',
        get_uploaded_image($src),
        $this->width,
        $this->height
      )
      : '';
  }
}