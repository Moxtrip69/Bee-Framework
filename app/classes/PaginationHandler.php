<?php

/**
 * @author Joystick
 * @version 1.0.2
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
	private $limit      = 20;
	private $pattern    = '';
	private $pages      = 0;
	private $page       = 0;
	private $start      = 0;
	private $end        = 0;
	private $pagination = '';
	private $total      = 0;

	private $alignment  = '';
	private $styles     = '';
	private $order      = '';
	private $direction  = 'DESC';
	private $variable   = 'page';

	public function __construct()
	{
		//$this->pattern = strtok(CUR_PAGE, '?');
		$this->pattern = CUR_PAGE;
	}

	/**
	 * Establece el query base para la base de datos
	 *
	 * @param string $query
	 * @return void
	 */
	function setBaseQuery(string $query)
	{
		$this->query = $query;	
	}

	/**
	 * Establece el límite de registros
	 *
	 * @param integer $rpp
	 * @return void
	 */
	function setRecordsPerPage(int $rpp)
	{
		$this->limit = $rpp;
	}

	/**
	 * Establece el valor de la variable usada en los parámetros GET de la petición
	 *
	 * @param string $variable
	 * @return void
	 */
	function setGetVariable(string $variable)
	{
		$this->variable = $variable;
	}

	/**
	 * Establece la dirección de los resultados regresados
	 *
	 * @param string $direction
	 * @return void
	 */
	function setDirection(string $direction)
	{
		$this->direction = strtoupper($direction);
	}

	/**
	 * Regresa el total de filas encontradas
	 *
	 * @return int
	 */
	public function get_total_rows()
	{
		$counted     = parent::query($this->query, $this->params);
		$this->total = !empty($counted) ? count($counted) : 0;
		return $this->total;
	}

	/**
	 * Calcula el total de páginas necesarias
	 *
	 * @return int
	 */
	public function calculate_pages()
	{
		$this->pages = ceil($this->total / $this->limit);
		return $this->pages;
	}

	/**
	 * Regresa la página actual
	 *
	 * @return int
	 */
	public function current_page()
	{
		$this->page = min($this->pages, filter_input(INPUT_GET, $this->variable, FILTER_VALIDATE_INT, array("options" => array("default" => 1, "min" => 1))));
		$this->page = ($this->page < 1) ? 1 : $this->page;
		return $this->page;
	}

	/**
	 * Calcula el offset necesario basado en el límite y total de registros por página
	 *
	 * @return int
	 */
	public function calculate_offset()
	{
		$this->offset = ($this->page - 1) * $this->limit; // 1 - 1 = 0 * 5 = 0
		$this->start  = $this->offset + 1;
		$this->end    = min(($this->offset + $this->limit), $this->total);
		return $this->offset;
	}

	/**
	 * Regresa las filas encontradas
	 *
	 * @return mixed
	 */
	public function get_rows()
	{
		$this->query .= strpos($this->query, 'ASC') === false && strpos($this->query, 'DESC') === false ? " {$this->direction}" : '';
		$this->query .= " LIMIT {$this->offset}, {$this->limit}";
		$this->rows   = parent::query($this->query, $this->params);
		return $this->rows;
	}

	/**
	 * Crea la navegación para paginar
	 *
	 * @return string
	 */
	private function create_pagination()
	{
		$pagination  = '<ul class="mt-5 pagination bee-pagination-wrapper ' . $this->alignment . '">';
		$pagination .=
		'<li class="page-item ' . ($this->page == 1 ? 'disabled' : '') . '">
			<a class="page-link" href="' . build_url($this->pattern, [$this->variable => $this->page - 1], false, false) . '" title="Anterior">&laquo;</a>
		</li>';

		// Current page
		for ($i = 1; $i <= $this->pages; $i++) {
			$pagination .= 
			'<li class="page-item bee-pagination-item ' . ($this->page == $i ? 'active disabled' : '') . '" data-page="' . $this->page . '">
				<a class="page-link" href="' . build_url($this->pattern, [$this->variable => $i], false, false) . '" >' . $i . '</a>
			</li>';
		}

		$pagination .=
		'<li class="page-item ' . ($this->page >= $this->pages ? 'disabled' : '') . '">
			<a class="page-link" href="' . build_url($this->pattern, [$this->variable => $this->page + 1], false, false) . '" title="Siguiente">&raquo;</a>
		</li>';
		$pagination .= '</ul>';

		## Links de paginación dinámicos
		$this->pagination  = $pagination;
		$this->pagination .= sprintf(
			'<small class="text-muted">Página %s de %s, mostrando %s-%s de %s resultados.</small>', 
			$this->page, 
			$this->pages, 
			$this->start, 
			$this->end, 
			$this->total
		);
		return $this->pagination;
	}

	/**
	 * Regresa el array con información de registros, páginas y navegación
	 *
	 * @return array
	 */
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

	/**
	 * Genera un query de paginación y procesa los elementos regresando
	 * la navegación y registros encontrados
	 *
	 * @param string $sql
	 * @param array $params
	 * @param integer $rpp
	 * @return array
	 */
	public static function paginate(string $sql, array $params = [], int $rpp = 20)
	{
		$self         = new self();
		$self->query  = $sql;
		$self->params = $params;
		$self->limit  = $rpp;
		return $self->launch();
	}
}
