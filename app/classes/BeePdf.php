<?php

use Dompdf\Dompdf;
use Dompdf\Exception as DomException;
use Dompdf\Options;

class BeePdf
{
	/**
	 * El contenido del PDF
	 *
	 * @var string
	 */
	private $content      = null;

	/**
	 * La fuente por defecto
	 *
	 * @var string
	 */
	private $font         = 'Arial';     // Por defecto

	/**
	 * La orientación del documento PDF
	 *
	 * @var string
	 */
	private $orientation  = 'portrait';  // portrait o landscape

	/**
	 * El tamaño del documento PDF
	 *
	 * @var string
	 */
	private $size         = "A4";

	/**
	 * Charset por defecto del documento PDF
	 *
	 * @var string
	 */
	private $charset      = "UTF-8";

	/**
	 * El nombre del documento PDF
	 *
	 * @var string
	 */
	private $filename     = null;

	/**
	 * Ruta en el servidor para guardar los documentos por defecto
	 *
	 * @var string
	 */
	private $path_to_save = UPLOADS;

	/**
	 * Path completo al documento PDF
	 *
	 * @var string
	 */
	private $path_to_file = null;

	/**
	 * Define si se hará por defecto stream de información en el explorador
	 *
	 * @var boolean
	 */
	private $streamPdf   = false;


	public function __construct(string $content = null, bool $download = false, bool $save_to_file = false)
	{
		if ($content !== null) {
			$this->content      = $content;
			$this->filename     = generate_filename() . '.pdf';
			$this->path_to_file = $this->path_to_save . $this->filename;

			try {
				$pdf = new self();
				$pdf->create($this->filename, $this->content, $download, $save_to_file);
				return $pdf;

			} catch (DomException $e) {
				throw new Exception($e->getMessage());
			}
		}
	}

	/**
	 * Crea un nuevo documento PDF
	 *
	 * @param string|null $filename
	 * @param string $content
	 * @param boolean $download
	 * @param boolean $save_to_file
	 * @return void
	 */
	public function create(string $filename = null, string $content, bool $download = false, bool $save_to_file = false)
	{
		$this->filename     = ($filename === null ? generate_filename() : $filename) . '.pdf';
		$this->content      = $content;
		$this->path_to_file = $this->path_to_save . $this->filename;

		// Opciones de configuración generales de la librería
		$options = new Options();
		$options->set('defaultFont'            , $this->font);
		$options->set('defaultPaperSize'       , $this->size);
		$options->set('defaultPaperOrientation', $this->orientation);
		$options->set('isRemoteEnabled'        , true);

		// Creación del PDF en curso
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($this->content, $this->charset);

		// Render the HTML as PDF
		$dompdf->render();

		// Guardar el contenido a un archivo en disco
		if ($save_to_file === true) {
			file_put_contents($this->path_to_save . $this->filename, $dompdf->output());
		}

		// Output the generated PDF to Browser
		if ($this->streamPdf === true) {
			$dompdf->stream($this->filename, ["Attachment" => ($download === true ? true : false)]);
		}
	}

	/**
	 * Regresa el nombre del archivo PDF
	 *
	 * @return string
	 */
	public function get_filename()
	{
		return $this->filename;
	}

	/**
	 * Regresa el path completo al archivo PDF
	 *
	 * @return string
	 */
	public function get_file()
	{
		return $this->path_to_file . $this->filename;
	}

	/**
	 * Establece el tamaño del documento
	 *
	 * @param string $size
	 * @return void
	 */
	function setSize(string $size)
	{
		$this->size = $size;
	}

	/**
	 * Establece la orientación del documento disponible portrait y landscape
	 *
	 * @param string $orientation
	 * @return void
	 */
	function setOrientation(string $orientation)
	{
		$this->orientation = $orientation;
	}

	/**
	 * Define si se hará stream de la información del pdf
	 * en el explorador por defecto al crearse el PDF
	 *
	 * @param boolean $stream
	 * @return void
	 */
	function streamPdf(bool $stream)
	{
		$this->streamPdf = $stream;
	}

	/**
	 * Método setter para las propiedades
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		if (!isset($this->{$key})) {
			throw new Exception(sprintf('No existe la propiedad %s', $key));
		}

		$this->{$key} = $value;

		return $this->{$key};
	}
}
