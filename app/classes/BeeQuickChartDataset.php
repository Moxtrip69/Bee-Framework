<?php 

class BeeQuickChartDataset
{
  /**
   * Etiqueta de dataset
   *
   * @var string|null
   */
  private ?string $label               = null;

  /**
   * Toda la información del dataset y sus configuraciones
   *
   * @var array
   */
  private array $dataset               = [];

  /**
   * Información a ser graficada
   *
   * @var array
   */
  private array $data                  = [];

  /**
   * Color de fondo
   *
   * @var string
   */
  private string $backgroundColor      = '';

  /**
   * Color del borde
   *
   * @var string
   */
  private string $borderColor          = '';

  /**
   * Color de los puntos
   *
   * @var string
   */
  private string $pointBackgroundColor = '';

  /**
   * Rellenar área debajo de las curvas
   *
   * @var bool
   */
  private bool $fill                   = true;

  /**
   * Ancho y separación de líneas punteadas
   *
   * @var array
   */
  private array $borderDash            = [10, 5];

  /**
   * Tamaño de los puntos
   *
   * @var int
   */
  private int $pointRadius             = 3;

  /**
   * Tipo de puntos
   *
   * @var string
   */
  private string $pointStyle           = 'dot';

  /**
   * Tipos de puntos disponibles
   *
   * @var array
   */
  private array $pointStyles           = ['dot', 'triangle', 'rect', 'star', 'cross'];

  function __construct()
  {
    $colors                     = $this->generateShades($this->randomColor());
    $this->backgroundColor      = $this->hexToRgb($colors[1], 0.6);
    $this->borderColor          = $this->hexToRgb($colors[0], 0.6);
    $this->pointBackgroundColor = $this->hexToRgb($colors[1], 1);
  }

  /**
   * Establece el label o etiqueta de un dataset
   *
   * @param string $label
   * @return void
   */
  function setLabel(string $label)
  {
    $this->label = $label;
  }

  /**
   * Establece el valor da $data que es la información a ser graficada
   *
   * @param array $data
   * @return void
   */
  function setData(array $data)
  {
    $this->data = $data;
  }

  /**
   * Establece el color base y de los puntos de la gráfica y su opacidad en caso de requerirla
   *
   * @param string $hexColor
   * @param integer $alpha
   * @return void
   */
  function setBaseColor(string $hexColor, float $alpha = 1)
  {
    $colors                     = $this->generateShades($hexColor);
    $this->backgroundColor      = $this->hexToRgb($colors[1], $alpha);
    $this->borderColor          = $this->hexToRgb($colors[0], $alpha);
    $this->pointBackgroundColor = $this->hexToRgb($colors[1], 1);
  }

  /**
   * Establece el color de fondo de los elementos de la gráfica
   *
   * @param string $hexColor
   * @param integer $alpha
   * @return void
   */
  function setBackgroundColor(string $hexColor, float $alpha = 1)
  {
    $this->backgroundColor = $this->hexToRgb($hexColor, $alpha);
  }

  /**
   * Establece el color del borde y su transparencia
   *
   * @param string $borderColor
   * @param integer $alpha
   * @return void
   */
  function setBorderColor(string $borderColor, float $alpha = 1)
  {
    $this->borderColor = $this->hexToRgb($borderColor, $alpha);
  }

  /**
   * Establece el tipo de punto para los elementos de la gráfica
   *
   * @param string $pointStyle
   * @return void
   */
  function setPointStyle(string $pointStyle)
  {
    $this->pointStyle = !in_array($pointStyle, $this->pointStyles) ? 'dot' : $pointStyle;
  }

  /**
   * Establece el radio o tamaño de los puntos
   *
   * @param int $pointRadius
   * @return void
   */
  function setPointRadius(int $pointRadius)
  {
    $this->pointRadius = $pointRadius;
  }

  /**
   * Establece si debería o no llenarse el área debajo de las curvas de algunos tipos de gráficas
   *
   * @param bool $fill
   * @return void
   */
  function setFill(bool $fill)
  {
    $this->fill = $fill;
  }

  /**
   * Establece el ancho y espaciado de las líneas de los bordes de las gráficas
   *
   * @param float $borderWidth
   * @param float $borderSpacing
   * @return void
   */
  function setBorder(float $borderWidth, float $borderSpacing)
  {
    $this->borderDash = [$borderWidth, $borderSpacing];
  }

  /**
   * Convierte un color hexadecimal con transparencia a un color RGB
   *
   * @param string $hexColor
   * @param integer $alpha
   * @return string
   */
  private function hexToRgb(string $hexColor, float $alpha = 1)
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

  /**
   * Genera un cólor random rgb
   *
   * @return string
   */
  private function randomColor() {
    $r = mt_rand(100, 255); // R dentro del rango 100-255 para tonos más claros
    $g = mt_rand(100, 255); // G dentro del rango 100-255 para tonos más claros
    $b = mt_rand(100, 255); // B dentro del rango 100-255 para tonos más claros

    return "rgb($r, $g, $b)";
  }

  /**
   * Genera dos variables de un color, una más clara y otra más obscura
   *
   * @param string $colorHex
   * @param float $factorClaro
   * @param float $factorOscuro
   * @return array
   */
  function generateShades(string $colorHex, float $factorClaro = 1.2, float $factorOscuro = 0.8)
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

  /**
   * Formatea el dataset en el formato requerido para ser utilizado
   *
   * @return array
   */
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

  /**
   * Devuelve el dataset formateado
   *
   * @return array
   */
  function getDataset()
  {
    return $this->formatDataset();
  }
}