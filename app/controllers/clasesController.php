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

use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

/**
 * Plantilla general de controladores
 * @version 1.0.2
 *
 * Controlador de clases
 */
class clasesController extends Controller implements ControllerInterface {
  function __construct()
  {
    // Prevenir el ingreso si nos encontramos en producción y esta ruta es sólo para desarrollo o pruebas
    if (!is_local()) {
      Redirect::to(DEFAULT_CONTROLLER);
    }

    // Ejecutar la funcionalidad del Controller padre
    parent::__construct();

    // Validación de sesión de usuario, descomentar si requerida
    // if (!Auth::validate()) {
    //  Flasher::new('Debes iniciar sesión primero.', 'danger');
    //  Redirect::to('login');
    // }

    register_scripts([JS . 'clases.js?v=' . get_asset_version()], 'Scripts para las clases en vivo');
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

    $this->setTitle('Clases en vivo');
    $this->addToData('concepts', $conceptos);
    $this->setView('index');
    $this->render();
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
        'tipografia'     => 'Verdana'
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

  function memes()
  {
    $this->addToData('title', 'Recopilación de memes');
    $this->setView('memes');
    $this->render();
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////// CLASE EN VIVO #7
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function autoguardado()
  {
    // Creamos el formulario para nuestras noticias
    $form = new BeeFormBuilder('autosave-form', 'autosaveForm');
    $form->addTextField('titulo', 'Título de la noticia', ['form-control'], 'titulo');
    $form->addHiddenField('id', 'ID', ['form-control'], 'id');
    $form->addTextareaField('contenido', 'Cuerpo de la noticia', 5, 10, ['form-control'], 'contenido');
    $form->addCustomFields(insert_inputs());
    $form->addButton('submit', 'submit', 'Guardar noticia', ['btn btn-success'], 'btnSubmit');

    // Nueva forma de trabajar la lógica de las rutas
    $this->addToData('title', 'Autoguardado');
    $this->addToData('form' , $form->getFormHtml());
    $this->setView('autosave');
    $this->render();
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////// CÓDIGOS QR
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function qr()
  {
    // Formulario
    $form = new BeeFormBuilder('qrForm', 'qrForm', [], 'clases/post_qr');
    $form->addCustomFields(insert_inputs());
    $form->addTextField('nombre', 'Nombre del negocio', ['form-control'], 'nombre', true, 'Pancho Villas Inc');
    $form->addEmailField('email', 'Correo electrónico de contacto', ['form-control'], 'email', true, 'jslocal@localhost.com');
    $form->addTextField('url', 'Sitio web del negocio', ['form-control'], 'url', true, 'https://www.joystick.com.mx');
    $form->addButton('submit', 'submit', 'Generar', ['btn btn-success'], 'submit');

    // Información para la vista
    $this->setTitle('Generando códigos QR');
    $this->addToData('form', $form->getFormHtml());
    $this->setView('qrcodes');
    $this->render();
  }

  function post_qr()
  {
    try {
      if (!check_posted_data(['nombre','email','url'], $_POST)) {
        throw new Exception('Completa el formulario por favor.');
      }

      // Datos de contacto
      array_map('sanitize_input', $_POST);
      $nombre = $_POST["nombre"];
      $email  = $_POST["email"];
      $url    = $_POST["url"];

      // Combinar los datos en una cadena de texto
      $informacionDeContacto  = "Nombre de la empresa: $nombre\n";
      $informacionDeContacto .= "Correo electrónico: $email\n";
      $informacionDeContacto .= "Sitio web: $url";

      // Create QR code
      $qrCode = QrCode::create($informacionDeContacto);
      $qrCode->setEncoding(new Encoding('UTF-8'));
      $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow());
      $qrCode->setSize(500);
      $qrCode->setMargin(10);
      $qrCode->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());
      $qrCode->setForegroundColor(new Color(0, 0, 0));
      $qrCode->setBackgroundColor(new Color(255, 255, 255));

      // Agregar un logotipo
      $logo = Logo::create(IMAGES_PATH . 'bee_logo_white.png');
      $logo->setResizeToWidth(150);
      $logo->setPunchoutBackground(true);

      // Crear un label o etiqueta
      $label = Label::create('Mi etiqueta cool');
      $label->setTextColor(new Color(50, 50, 50));

      $writer = new PngWriter();
      $result = $writer->write($qrCode, $logo, $label);

      // Hacer output en pantalla del resultado como imagen
      header('Content-Type: '.$result->getMimeType());
      echo $result->getString();

      // Guardar como archivo
      // $result->saveToFile(UPLOADS . '/qrcode.png');

      // Generar un URI para incluir en una etiqueta img con base64
      $dataUri = $result->getDataUri();
      // echo sprintf('<img src="%s" alt="QR generado" style="border: 1px solid #ebebeb; border-radius: 20px;">', $dataUri);

    } catch (Exception $e) {
      Flasher::error($e->getMessage());
      Redirect::back();
    }
  }
}