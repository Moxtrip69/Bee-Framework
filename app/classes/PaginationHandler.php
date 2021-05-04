<?php 

/**
* @author Joystick
* @version 1.0.0
*
*/
class PaginationHandler extends Model
{
	/**
	* Establecimiento de parámetros necesarios
	* @param
	*
	**/
	private $query;
	private $params     = [];
	private $rows       = [];
	private $offset;
	private $limit      = 25;
	private $pattern    = '';
	private $pages      = 0;
	private $page       = 0;
	private $start      = 0;
	private $end        = 0;
	private $pagination = '';

	private $alignment  = '';
	private $styles     = '';
	private $order      = '';
	private $direction  = 'DESC';
	private $variable   = 'page';

	public function __construct()
	{
		$this->pattern = strtok(CUR_PAGE, '?');
	}
	
	public function get_total_rows()
	{
		$counted     = parent::query($this->query, $this->params);
		$this->total = !empty($counted) ? count($counted) : 0;
		return $this->total;
	}

	public function calculate_pages()
	{
		$this->pages = ceil($this->total / $this->limit);
		return $this->pages;
	}

	public function current_page()
	{
		$this->page = min($this->pages, filter_input(INPUT_GET, $this->variable , FILTER_VALIDATE_INT, array("options" => array("default" => 1 , "min" => 1))));
		$this->page = ($this->page < 1) ? 1 : $this->page;
		return $this->page;
	}

	public function calculate_offset()
	{
		$this->offset = ($this->page - 1) * $this->limit; // 1 - 1 = 0 * 5 = 0
		$this->start  = $this->offset + 1;
		$this->end    = min(($this->offset + $this->limit), $this->total);
		return $this->offset;
	}

	public function get_rows()
	{
		$this->query .= " LIMIT {$this->offset}, {$this->limit}";
		$this->rows   = parent::query($this->query, $this->params);
		return $this->rows;
	}

	public function create_pagination()
	{
		$pagination = '<ul class="mt-5 pagination '.$this->alignment.'">';
		$pagination .= 
		'<li class="page-item '.($this->page == 1 ? 'disabled' : '').'">
			<a class="page-link" href="'.buildURL($this->pattern, [$this->variable => $this->page - 1], false, false).'" title="Anterior">&laquo;</a>
		</li>';

		for ($i = 1; $i <= $this->pages; $i++) {
				// Current page
				$pagination .= '
				<li class="page-item '.($this->page == $i ? 'active disabled' : '').'">
					<a class="page-link" href="'.buildURL($this->pattern, [$this->variable => $i], false, false).'" >'.$i.'</a>
				</li>
				';
		}

		$pagination .= 
		'<li class="page-item '.($this->page >= $this->pages ? 'disabled' : '').'">
			<a class="page-link" href="'.buildURL($this->pattern, [$this->variable => $this->page + 1], false, false).'" title="Siguiente">&raquo;</a>
		</li>';
		$pagination .= '</ul>';

		## Links de paginación dinámicos
		$this->pagination = $pagination;
		$this->pagination .= sprintf('<small class="text-muted">Página %s de %s, mostrando %s-%s de %s resultados.</small>', $this->page, $this->pages, $this->start, $this->end, $this->total);
		return $this->pagination;
	}

	public function launch()
	{
		return 
		[
			'total'      => $this->get_total_rows(),
			'pages'      => $this->calculate_pages(),
			'page'       => $this->current_page(),
			'offset'     => $this->calculate_offset(),
			'rows'       => $this->get_rows(),
			'pagination' => $this->create_pagination()
		];
	}

	public static function paginate($sql, $params = [], $rpp = 20)
	{
		$self         = new self();
		$self->query  = $sql;
		$self->params = $params;
		$self->limit  = $rpp;
		return $self->launch();
	}
}