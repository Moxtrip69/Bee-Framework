<?php 
use Dompdf\Dompdf;
use Dompdf\Option;
use Dompdf\Exception as DomException;
use Dompdf\Options;

class BeePdf {

	private $template     = null;
	private $html         = null;
	private $content      = null;
	
	private $font         = 'Arial';     // Por defecto
	private $orientation  = 'portrait';  // portrait o landscape
	private $size         = "A4";
	private $lgn          = 'es';        // Por defecto
	private $charset      = "UTF-8";
	private $margin       = array(20 , 15 , 20 , 20);

	private $filename     = null;
	private $filesize     = 0;

	private $path_to_save = UPLOADS;
	private $path_to_file = null;
	

	public function __construct($content = null, $download = false, $save_to_file = false)
	{
		if($content !== null){
      $this->content      = $content;
      $this->filename     = generate_filename().'.pdf';
      $this->path_to_file = $this->path_to_save.$this->filename;

			try {
				
				$pdf = new self();
				$pdf->create($this->filename, $this->content, $download, $save_to_file);
        return $pdf;
				
			} catch (DomException $e) {
				throw new Exception($e->getMessage());
			}
		}

		return $this;
	}

	public function create($filename = null, $content, $download = false, $save_to_file = false)
	{
		$this->filename     = ($filename === null ? generate_filename() : $filename).'.pdf';
    $this->content      = $content;
    $this->path_to_file = $this->path_to_save.$this->filename;

		// instantiate and use the dompdf class
		$options = new Options();
		$options->set('defaultFont'             , $this->font);
		$options->set('isRemoteEnabled'         , true);
		$options->setIsRemoteEnabled(true);
		$options->set('defaultPaperSize'        , $this->size);
		$options->set('defaultPaperOrientation' , $this->orientation);

		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($this->content, $this->charset);

		// Render the HTML as PDF
		$dompdf->render();
		
		// Guardar el contenido a un archivo en disco
		if ($save_to_file === true) {
			file_put_contents($this->path_to_save.$this->filename, $dompdf->output());
		}

		// Output the generated PDF to Browser
		$dompdf->stream($this->filename, ["Attachment" => ($download === true ? true : false)]);

		return true;
	}

	public function get_filename()
	{
		return $this->filename;
	}

  public function get_file()
  {
    return $this->path_to_file.$this->filename;
  }

  public function __set($key, $value)
  {
    if (!isset($this->{$key})) {
      throw new Exception(sprintf('No existe la propiedad %s', $key));
    }

    $this->{$key} = $value;

    return $this->{$key};
  }
}
