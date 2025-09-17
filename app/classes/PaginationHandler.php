<?php

/**
 * @author Joystick
 * 
 * Contribuido por
 * @author Eddy Joel Barranzuela Maldonado / Miembro Premium
 * 
 * @version 1.1.0
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
	private $isAjax     = false;

	private $alignment  = '';
	private $selector   = 'BtnPaginate';
	private $styles     = '';
	private $order      = '';
	private $direction  = 'DESC';
	private $variable   = 'page';

	/**
	 * Determina si se recortará o no la navegación
	 *
	 * @var boolean
	 */
	private $shorten    = false;

	/**
	 * Cantidad de enlaces a mostrar a la vez al recortar la navegación
	 *
	 * @var integer
	 */
	private $maxItems   = 5;

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
	 * Parámetros requeridos para el query
	 * 
	 * @return void
	 */
	function setParams(array $params)
	{
		$this->params = $params;
	}

	/**
	 * Define si el paginador está siendo usado en una petición
	 * AJAX para solucionar el problema con los enlaces y trabajar
	 * con javascript
	 *
	 * @param boolean $isAjax
	 * @return void
	 */
	function setIsAjax(bool $isAjax)
	{
		$this->isAjax = $isAjax;
	}
	
	/**
	 * Establece la clase que será asignada a los botones de paginación
	 * 
	 * @param string $class
	 * 
	 */
	function setSelector(string $selector){
		$this->selector = $selector;
	}
	
	/**
	 * Define si deberán recortarse el número de enlaces o botones
	 * en la paginación para reducir el ancho u overflow
	 *
	 * @param boolean $shorten
	 * @return void
	 */
	function setShorten(bool $shorten)
	{
		$this->shorten = $shorten;
	}

	/**
	 * Define cuantos botones de paginación mostrar al momento
	 * el resto serán cortados si shorten está activado
	 *
	 * @param integer $items
	 * @return void
	 */
	function setMaxItems(int $items)
	{
		// Para solucionar problemas al momento de mostrar los enlaces y sea consistente
		if ($items % 2 == 0) {
			$items += 1;
		}

		$this->maxItems = $items;
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
		// Determinar que etiqueta usar en caso de ser una petición AJAX
		$htmlTag     = $this->isAjax === true ? 'button' : 'a';

		// Crear el html de la paginación
		$pagination  = '<ul class="mt-5 pagination bee-pagination-wrapper ' . $this->alignment . '">';

		// Botón de anterior
		$pagination .= sprintf(
			'<li class="page-item %s">
				<%s class="page-link %s" data-page="%s" href="%s" title="Anterior">&laquo;</%s>
			</li>',
			$this->page == 1 ? 'disabled' : '',
			$htmlTag,
			$this->selector,
			$this->page == 1 ? 1 : $this->page - 1,
			build_url($this->pattern, [$this->variable => $this->page == 1 ? 1 : $this->page - 1], false, false),
			$htmlTag
		);

		// Iteración de todas las páginas y botones
		if ($this->pages > $this->maxItems && $this->shorten === true) {
			// Definir el rango de páginas a mostrar
			$steps      = floor($this->maxItems / 2);
			$start_page = max(1, $this->page - $steps);
			$end_page   = min($this->pages, $this->page + $steps);

			// Ajustar el rango si hay menos de 10 páginas visibles al inicio o al final
			if ($end_page - $start_page + 1 < $this->maxItems) {
				if ($start_page == 1) {
					$end_page   = min($start_page + $this->maxItems - 1, $this->pages);
				} else {
					$start_page = max(1, $end_page - $this->maxItems + 1);
				}
			}

			for ($i = $start_page; $i <= $end_page; $i++) {
				$pagination .= sprintf(
					'<li class="page-item %s">
						<%s class="page-link %s" data-page="%s" href="%s" title="Página %s">%s</%s>
					</li>',
					$this->page == $i ? 'active disabled' : '',
					$htmlTag,
					$this->selector,
					$i, // data-page
					build_url($this->pattern, [$this->variable => $i], false, false),
					$i, // Texto en Alt
					$i, // Texto dentro de la etiqueta
					$htmlTag
				);
			}
		} else {
			for ($i = 1; $i <= $this->pages; $i++) {
				$pagination .= sprintf(
					'<li class="page-item %s">
						<%s class="page-link %s" data-page="%s" href="%s" title="Página %s">%s</%s>
					</li>',
					$this->page == $i ? 'active disabled' : '',
					$htmlTag,
					$this->selector,
					$i, // data-page
					build_url($this->pattern, [$this->variable => $i], false, false),
					$i, // Texto en Alt
					$i, // Texto dentro de la etiqueta
					$htmlTag
				);
			}
		}

		// Botón de siguiente
		$pagination .= sprintf(
			'<li class="page-item %s">
				<%s class="page-link %s" data-page="%s" href="%s" title="Siguiente">&raquo;</%s>
			</li>',
			$this->page >= $this->pages ? 'disabled' : '',
			$htmlTag,
			$this->selector,
			$this->page >= $this->pages ? $this->pages : $this->page + 1,
			build_url($this->pattern, [$this->variable => $this->page >= $this->pages ? $this->pages : $this->page + 1], false, false),
			$htmlTag
		);

		$pagination .= '</ul>';

		// Links de paginación dinámicos
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
	 * @param string $sql El SQL base de la petición
	 * @param array $params Parámetros requeridos en el query
	 * @param integer $rpp Los registros por página a mostrar
	 * @param string $selector El selector del elemento buton o a
	 * @param bool $isAjax Determina si es petición ajax o no
	 * @param bool $shorten
	 * @param int $maxItems
	 * @return array
	 */
	public static function paginate(string $sql, array $params = [], int $rpp = 20, ?string $selector = null, bool $isAjax = false, bool $shorten = false, ?int $maxItems = null)
	{
		$self         = new self();
		$self->query  = $sql;
		$self->params = $params;
		$self->limit  = $rpp;
		
		// Nuevas configuraciones para el paginador
		if ($selector !== null) {
			$self->selector = $selector;
		}
		
		$self->isAjax = $isAjax; // Determina si será una petición ajax o no en donde se usará
		$self->shorten = $shorten; // Será recortada o no la navegación

		if ($maxItems !== null) {
			$self->setMaxItems($maxItems); // Número máximo de enlaces a la vez
		}

		return $self->launch();
	}
}
