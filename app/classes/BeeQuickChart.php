<?php 

// TODO: Tipo bubble, scatter, progressBar revisar su sintaxis y ajustes específicos

class BeeQuickChart
{
  /**
   * El endpoint oficial de la API de QuickCharts
   *
   * @var string
   */
  private $endpoint = 'https://quickchart.io/chart';

  /**
   * Toda la información de la gráfica a ser generada
   *
   * @var array
   */
  private $chart    = [];

  /**
   * Ancho de la gráfica a generar (imagen)
   *
   * @var integer
   */
  public $width     = 500;

  /**
   * Alto de la gráfica a generar (imagen)
   *
   * @var integer
   */
  public $height    = 500;

  /**
   * El tipo de gráfica a generar
   * opciones disponibles según la documentación de Chartjs
   * bar
   * horizontalBar 
   * line
   * radar
   * pie
   * doughnut
   * polarArea
   * scatter
   * bubble
   * radialGauge
   * gauge
   * progressBar
   *
   * @var string
   */
  private $type;

  /**
   * Tipos de gráficas aceptados hasta el momento
   *
   * @var array
   */
  private $acceptedTypes = 
    [
      'bar',
      'horizontalBar',
      'line',
      'pie',
      'doughnut',
      'progressBar',
      'scatter',
      'bubble',
      'gauge',
      'radar'
    ];
    
  /**
   * Conjunto de datasets e información de la gráfica a ser generada
   *
   * @var array
   */
  private $data = [];

  /**
   * Labels horizontales o principales de la gráfica
   *
   * @var array
   */
  private $labels = [];

  /**
   * Conjuntos de información a ser utilizados para la generación de la gráfica
   *
   * @var array
   */
  private $datasets = [];

  /**
   * URL para cargar la imagen de forma dinámica
   *
   * @var string
   */
  private $url;

  function __construct($type = null)
  {
    $this->type = $type === null || !in_array($type, $this->acceptedTypes) ? 'bar' : $type;
  }

  /**
   * Establece el tamaño de la gráfica o imagen
   *
   * @param integer $width
   * @param integer $height
   * @return void
   */
  function setSize(int $width = 500, int $height = 500)
  {
    $this->width  = $width;
    $this->height = $height;  
  }

  /**
   * Organiza y acomoda la estructura general del contenido de la gráfica
   *
   * @return array
   */
  function formatData()
  {
    $this->data =
    [
      'labels'   => $this->labels,
      'datasets' => $this->datasets
    ];

    return $this->data;
  }

  /**
   * Establece las etiquetas o lables de la gráfica (regularmente en el eje Y)
   *
   * @param array $labels
   * @return void
   */
  function setLabels(array $labels)
  {
    $this->labels = $labels;
  }

  /**
   * Agrega un dataset de información a la gráfica
   * 
   * @param string $label
   * @param array $data
   * @return void
   */
  function addDataset(BeeQuickChartDataset $dataset)
  {
    $this->datasets[] = $dataset->getDataset(); // push del dataset
  }

  /**
   * Procesa y construye el array de información de la gráfica
   *
   * @return array
   */
  private function buildChart()
  {
    $this->chart =
    [
      'type' => $this->type,
      'data' => $this->formatData(),
    ];

    return $this->chart;
  }

  /**
   * Regresa el array de información de la gráfica a generar completamente
   *
   * @return array
   */
  function getChart()
  {
    return $this->buildChart();
  }

  /**
   * Formatea y genera la URL para petición GET al endpoint de Quickcharts
   *
   * @return string
   */
  private function processUrl()
  {
    $this->url = sprintf('%s?width=%s&height=%s&chart=%s',
      $this->endpoint,
      $this->width,
      $this->height,
      urlencode(json_encode($this->getChart()))
    );

    return $this->url;
  }

  /**
   * Regresa la URL completa y formateada para cargar la gráfica cómo imagen
   *
   * @return string
   */
  function getUrl()
  {
    return $this->processUrl();
  }

  /**
   * Guarda la imagen como una imagen en disco
   *
   * @return bool
   */
  function saveToImage(string $filename = null)
  {
    // URL del recurso
    $url      = $this->getUrl();

    // Nombre del archivo de la imagen
    $filename = $filename === null ? generate_filename() . '.jpg' : $filename . '.jpg';
    $path     = UPLOADS . $filename; // PATH en disco

    // Copiar el recurso remoto y guardarlo en el servidor como archivo estático
    if (!copy($url, $path)) {
      throw new Exception('Hubo un problema al guardar el archivo local.');
    }

    return $filename;
  }
}