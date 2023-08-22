<?php 

class BeeQuickChartDataset
{
  private ?String $label               = null;
  private Array $dataset               = [];
  private Array $data                  = [];
  private String $backgroundColor      = '';
  private String $borderColor          = '';
  private String $pointBackgroundColor = '';
  private Bool $fill                   = true;
  private Array $borderDash            = [10, 5];
  private Int $pointRadius             = 3;
  private String $pointStyle           = 'dot';
  private Array $pointStyles           = ['dot', 'triangle', 'rect', 'star', 'cross'];

  function __construct()
  {
    $colors                     = $this->generateShades($this->randomColor());
    $this->backgroundColor      = $this->hexToRgb($colors[1], 0.6);
    $this->borderColor          = $this->hexToRgb($colors[0], 0.6);
    $this->pointBackgroundColor = $this->hexToRgb($colors[1], 1);
  }

  function setLabel(String $label)
  {
    $this->label = $label;
  }

  function setData(Array $data)
  {
    $this->data = $data;
  }

  function setBaseColor(String $hexColor, Float $alpha = 1)
  {
    $colors                     = $this->generateShades($hexColor);
    $this->backgroundColor      = $this->hexToRgb($colors[1], $alpha);
    $this->borderColor          = $this->hexToRgb($colors[0], $alpha);
    $this->pointBackgroundColor = $this->hexToRgb($colors[1], 1);
  }

  function setBackgroundColor(String $hexColor, Float $alpha = 1)
  {
    $this->backgroundColor = $this->hexToRgb($hexColor, $alpha);
  }

  function setBorderColor(String $borderColor, Float $alpha = 1)
  {
    $this->borderColor = $this->hexToRgb($borderColor, $alpha);
  }

  function setPointStyle(String $pointStyle)
  {
    $this->pointStyle = !in_array($pointStyle, $this->pointStyles) ? 'dot' : $pointStyle;
  }

  function setPointRadius(Int $pointRadius)
  {
    $this->pointRadius = $pointRadius;
  }

  function setFill(Bool $fill)
  {
    $this->fill = $fill;
  }

  function setBorder(Float $borderWidth, Float $borderSpacing)
  {
    $this->borderDash = [$borderWidth, $borderSpacing];
  }

  private function hexToRgb(String $hexColor, Float $alpha = 1)
  {
    // Elimina el "#" si está presente
    $hex = str_replace("#", "", $hexColor);

    // Si el color es de 3 caracteres, expándelo a 6 caracteres duplicando cada carácter
    if (strlen($hex) == 3) {
      $hex = str_repeat($hex, 2);
    }
    
    // Convierte el valor hexadecimal a valores RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $a = $alpha > 1 ? 1 : ($alpha < 0 ? 0 : $alpha);

    return "rgba($r, $g, $b, $a)";
  }

  private function randomColor() {
    $r = mt_rand(100, 255); // R dentro del rango 100-255 para tonos más claros
    $g = mt_rand(100, 255); // G dentro del rango 100-255 para tonos más claros
    $b = mt_rand(100, 255); // B dentro del rango 100-255 para tonos más claros

    return "rgb($r, $g, $b)";
  }

  function generateShades(String $colorHex, Float $factorClaro = 1.2, Float $factorOscuro = 0.8)
  {
    // Elimina el "#" si está presente
    $colorHex     = str_replace("#", "", $colorHex);

    // Convierte el color hexadecimal a valores RGB
    $r            = hexdec(substr($colorHex, 0, 2));
    $g            = hexdec(substr($colorHex, 2, 2));
    $b            = hexdec(substr($colorHex, 4, 2));

    // Calcula colores más claros y más oscuros
    $factorClaro  = 1.2; // Puedes ajustar este factor para obtener el grado de claridad deseado
    $factorOscuro = 0.8; // Puedes ajustar este factor para obtener el grado de oscuridad deseado

    $rClaro       = min(255, $r * $factorClaro);
    $gClaro       = min(255, $g * $factorClaro);
    $bClaro       = min(255, $b * $factorClaro);

    $rOscuro      = max(0, $r * $factorOscuro);
    $gOscuro      = max(0, $g * $factorOscuro);
    $bOscuro      = max(0, $b * $factorOscuro);

    // Convierte los valores RGB de nuevo a formato hexadecimal
    $colorClaro   = sprintf("#%02X%02X%02X", $rClaro, $gClaro, $bClaro);
    $colorOscuro  = sprintf("#%02X%02X%02X", $rOscuro, $gOscuro, $bOscuro);

    return [$colorClaro, $colorHex, $colorOscuro];
  }

  private function formatDataset()
  {
    $this->dataset =
    [
      'label'                => $this->label,
      'data'                 => $this->data,
      'backgroundColor'      => $this->backgroundColor,
      'borderColor'          => $this->borderColor,
      'fill'                 => $this->fill,
      'borderDash'           => $this->borderDash,
      'pointRadius'          => $this->pointRadius,
      'pointBackgroundColor' => $this->pointBackgroundColor,
      'pointStyle'           => $this->pointStyle
    ];

    return $this->dataset;
  }

  function getDataset()
  {
    return $this->formatDataset();
  }
}