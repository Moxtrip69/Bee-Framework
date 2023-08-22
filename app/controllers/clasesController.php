<?php
/**
 * Este controlador es meramente para clases en vivo de la Academia
 * para facilitar el trabajo y mantenerlo sincronizado con los nuevos cambios del framework
 * si gustas puedes borrar este controlador y el paquete de vistas que vienen con él
 * 
 * He bloqueado el acceso en producción para tu seguridad en caso de que no borres dicho controlador
 * 
 * Gracias por tu apoyo.
 */

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Plantilla general de controladores
 * @version 1.0.2
 *
 * Controlador de clases
 */
class clasesController extends Controller {
  function __construct()
  {
    // Prevenir el ingreso si nos encontramos en producción y esta ruta es sólo para desarrollo o pruebas
    if (!is_local()) {
      Redirect::to(DEFAULT_CONTROLLER);
    }

    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //  Flasher::new('Debes iniciar sesión primero.', 'danger');
    //  Redirect::to('login');
    // }
  }
  
  function index()
  {
    $conceptos =
    [
      [
        'id'       => 2932,
        'sku'      => 'SUPERSKU123',
        'nombre'   => 'Clase en vivo para estudiantes',
        'cantidad' => 1,
        'precio'   => 199
      ],
      [
        'id'       => 8541,
        'sku'      => 'ABCS213',
        'nombre'   => 'Paquete de cursos premium',
        'cantidad' => 2,
        'precio'   => 300
      ],
      [
        'id'       => 8896,
        'sku'      => 'PFS2023',
        'nombre'   => 'Playera deportiva Vital Army',
        'cantidad' => 3,
        'precio'   => 499
      ],
      [
        'id'       => 8896,
        'sku'      => 'ACADEMY04',
        'nombre'   => 'Diseño editorial revista',
        'cantidad' => 1,
        'precio'   => 840
      ],
    ];
    $_SESSION['conceptos'] = $conceptos;

    $data = 
    [
      'title'    => 'Clases en vivo',
      'number'   => 6,
      'topic'    => 'Crea documentos y reportes en PDF con PHP y Dompdf.',
      'concepts' => $conceptos
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function post_generar_reporte()
  {
    try {
      if (!check_posted_data(['orientacion','tamano','cliente','email','direccion','bgColor'], $_POST)) {
        throw new Exception('Completa el formulario por favor.');
      }

      array_map('clean', $_POST);
      $bgColor     = $_POST["bgColor"];
      $orientation = $_POST["orientacion"];
      $size        = $_POST["tamano"];
      $client      = $_POST["cliente"];
      $email       = $_POST["email"];
      $address     = $_POST["direccion"];
      $textColor   = decideTextColor($bgColor); // negro blanco

      // Información de la empresa
      $companyName    = get_sitename();
      $companyAddress = 'Una calle #123, Ciudad de México, México, 15896';
      $companyUrl     = 'www.joystick.com.mx';

      // Información del pdf
      $quoteNumber = random_password(8, 'numeric');
      $pdfName     = sprintf('Cotización-%s.pdf', $quoteNumber);

      // Conceptos
      $concepts    = $_SESSION['conceptos'];

      // Gráfica
      $chart = new BeeQuickChart('bar');
      $chart->setSize(1000, 300);
      $chart->setLabels(['Enero', 'Febrero', 'Marzo', 'Abril']);
      $chart->addDataset('Ventas'  , [10, 20, 30, 25]);
      $chart->addDataset('Compras' , [15, 32, 55, 64]);
      $chart->addDataset('Saldo'   , [35, 52, 155, 84]);
      $chartUrl = $chart->getUrl();

      ////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////
      //////// Opciones de configuración de Dompdf
      ////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////
      $options = new Options();
      // $options->set('defaultFont', 'Courier'); // Configurar la fuente por defecto a usar
      // $options->set('fontDir', UPLOADS); // Directorio donde se encuentran las fuentes personalizadas.
      // $options->set('fontCache', ...); // Directorio para almacenar en caché de fuentes.
      // $options->set('isPhpEnabled', false); // Permite la ejecución de PHP en el contenido HTML (no recomendado por razones de seguridad).
      $options->set('dpi'                  , 300); // Resolución en puntos por pulgada para la renderización de imágenes (valor predeterminado: 96).
      $options->set('isHtml5ParserEnabled' , true); // Habilita el uso del parser HTML5 en lugar del parser más antiguo de HTML.
      $options->set('isRemoteEnabled'      , true); // Para poder hacer uso de recursos externos cómo imágenes

      ////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////
      //////// Generación del PDF
      ////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////////
      $dompdf = new Dompdf($options);

      // Configuración de tamaño y orientación
      $dompdf->setPaper($size, $orientation);
  
      // Cargando el contenido desde una plantilla externa
      $data =
      [
        'quote'          => $quoteNumber,
        'pdf'            => $pdfName,
        'date'           => date('Y-m-d'),
        'concepts'       => $concepts,
        'bgColor'        => $bgColor,
        'textColor'      => $textColor,
        'client'         => $client,
        'email'          => $email,
        'address'        => $address,
        'companyName'    => $companyName,
        'companyAddress' => $companyAddress,
        'companyUrl'     => $companyUrl,
        'tipografia'     => 'Verdana',
        'chart'          => $chartUrl
      ];

      // Recuerda, el módulo debe ir dentro de templates/modules/...
      $html = get_module('cotizacion', $data);

      // Definir el contenido del PDF
      $dompdf->loadHtml($html);

      // Procesar y renderizar el PDF, esto no hace que salga en pantalla aún
      $dompdf->render();

      // Hacer output de la información binaria del PDF para descargar o mostrar en pantalla al cliente
      $dompdf->stream($pdfName, ['Attachment' => false]);
      
    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}