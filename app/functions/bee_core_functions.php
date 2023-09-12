<?php

//////////////////////////////////////////////////
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as EmailException;
//////////////////////////////////////////////////

/**
 * Convierte el elemento en un objecto
 *
 * @param array $array
 * @return object
 */
function to_object($array)
{
	return json_decode(json_encode($array));
}

/**
 * Regresa el nombre de nuestra aplicación
 *
 * @return string
 */
function get_sitename()
{
	return SITE_NAME;
}

/**
 * Regresa la versión de nuestro sistema actual
 *
 * @return string
 */
function get_version()
{
	return SITE_VERSION;
}

/**
 * Regresa el nombre del framework Bee Framework
 *
 * @return string
 */
function get_bee_name()
{
	return BEE_NAME;
}

/**
 * Regresa la versión del framework actual
 *
 * @return string
 */
function get_bee_version()
{
	return BEE_VERSION;
}

/**
 * Devuelve el email general del sistema
 *
 * @return string
 */
function get_siteemail()
{
	return 'jslocal@localhost.com';
}

/**
 * Devuelve la URL del sitio configurada en la constante URL
 *
 * @return string
 */
function get_base_url()
{
	return URL;
}

/**
 * Devuelve el valor del controlador por defecto de la constante DEFAULT_CONTROLLER
 *
 * @return string
 */
function get_default_controller()
{
	return DEFAULT_CONTROLLER;
}

/**
 * Regresa la fecha de estos momentos
 *
 * @return string
 */
function now()
{
	return date('Y-m-d H:i:s');
}

/**
 * Hace output en el body como json
 *
 * @param array $json
 * @param boolean $die
 * @return string|bool
 */
function json_output($json, $die = true)
{
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json;charset=utf-8');

	if (is_array($json)) {
		$json = json_encode($json);
	}

	echo $json;

	if ($die === true) {
		die;
	}

	return true;
}

/**
 * Construye un nuevo string json
 	200 OK
	201 Created
	300 Multiple Choices
	301 Moved Permanently
	302 Found
	304 Not Modified
	307 Temporary Redirect
	400 Bad Request
	401 Unauthorized
	403 Forbidden
	404 Not Found
	410 Gone
	500 Internal Server Error
	501 Not Implemented
	503 Service Unavailable
	550 Permission denied
 *
 * @param integer $status
 * @param array $data
 * @param string $msg
 * @return string
 */
function json_build($status = 200, $data = [], $msg = '', $error_code = null)
{
	if (empty($msg) || $msg == '') {
		switch ($status) {
			case 200:
				$msg = 'OK';
				break;
			case 201:
				$msg = 'Created';
				break;
			case 300:
				$msg = 'Multiple Choices';
				break;
			case 301:
				$msg = 'Moved Permanently';
				break;
			case 302:
				$msg = 'Found';
				break;
			case 400:
				$msg = 'Invalid request';
				break;
			case 403:
				$msg = 'Access denied';
				break;
			case 404:
				$msg = 'Not found';
				break;
			case 500:
				$msg = 'Internal Server Error';
				break;
			case 550:
				$msg = 'Permission denied';
				break;
			default:
				break;
		}
	}

	$json =
		[
			'status'     => $status,
			'error'      => false,
			'error_code' => $error_code,
			'msg'        => $msg,
			'data'       => $data
		];

	if (in_array($status, [400, 403, 404, 405, 500])) {
		$json['error'] = true;
	}

	if ($error_code !== null) {
		$json['error_code'] = $error_code;
	}

	return json_encode($json);
}

/**
 * Regresa parseado un modulo
 *
 * @param string $view
 * @param array $data
 * @return bool|string
 */
function get_module($view, $data = [])
{
	$file_to_include = MODULES . $view . 'Module.php';
	$output = '';

	// Por si queremos trabajar con objeto
	$d = to_object($data);

	if (!is_file($file_to_include)) {
		return false;
	}

	ob_start();
	require $file_to_include;
	$output = ob_get_clean();

	return $output;
}

/**
 * Formatea un número a divisa
 *
 * @param float $amount
 * @param string $symbol
 * @return string
 */
function money($amount, $symbol = '$')
{
	return $symbol . number_format($amount, 2, '.', ',');
}

/**
 * Carga una opción de configuración de la db
 *
 * @param mixed $option
 * @return mixed
 */
function get_option($option)
{
	return optionModel::search($option);
}

/**
 * Generar un link dinámico con parámetros get y token
 * @param string $url
 * @param array $params
 * @param bool $redirection
 * @param bool $csrf
 * Actualizada por build_url
 * @since 1.1.4
 * 
 * @return string
 */
function buildURL($url, $params = [], $redirection = true, $csrf = true)
{
	return build_url($url, $params, $redirection, $csrf);
}

/**
 * Generar un link dinámico con parámetros get y token
 * @param string $url
 * @param array $params
 * @param bool $redirection
 * @param bool $csrf
 * @since 1.5.5
 * 
 * @return string
 */
function build_url($url, $params = [], $redirection = true, $csrf = true)
{
	// Formateo y parseo inicial de la URL pasada descomponiendo sus elementos
	$raw_url     = parse_url($url, PHP_URL_PATH);
	$query       = parse_url($url, PHP_URL_QUERY); // extraer parámetros existentes
	$query_array = [];

	// Sólo si ya hay parámetros existentes en la URL
	if (!empty($query)) {
		parse_str($query, $query_array); // convertir en array los parámetros
	}

	// Si requiere token csrf
	if ($csrf) {
		$query_array["_t"]          = CSRF_TOKEN;
	}

	// Si requiere redirección
	if ($redirection) {
		$query_array["redirect_to"] = CUR_PAGE;
	}

	// Si no es un array regresa la url original
	if (!is_array($params)) {
		return $url;
	}

	// Listando parámetros
	if (!empty($params)) {
		foreach ($params as $key => $value) {
			$query_array[$key] = $value;
		}
	}

	// Sólo si no está vacía la lista de parámetros para URL
	if (!empty($query_array)) {
		$args = http_build_query($query_array);
		$url  = sprintf("%s?%s", $raw_url, $args);
	}

	return $url;
}

/**
 * Loggea un registro en un archivo de logs del sistema, usado para debugging
 *
 * @param string $message
 * @param string $type
 * @param boolean $output
 * @return mixed
 */
function logger($message, $type = 'debug', $output = false)
{
	$types = ['debug', 'import', 'info', 'success', 'warning', 'error'];

	if (!in_array($type, $types)) {
		$type = 'debug';
	}

	$now_time = date("d-m-Y H:i:s");
	$message  = is_array($message) || is_object($message) ? print_r($message, true) : $message;
	$message  = "[" . strtoupper($type) . "] $now_time - $message";

	if (!is_dir(LOGS)) {
		mkdir(LOGS);
	}

	$filename = is_local() ? "dev_log.log" : "bee_log.log";
	if (!$fh = fopen(LOGS . $filename, 'a')) {
		error_log(sprintf('Can not open this file on %s', LOGS . 'bee_log.log'));
		return false;
	}

	fwrite($fh, "$message\n");
	fclose($fh);
	if ($output) {
		print "$message\n";
	}

	return true;
}

/**
 * Loggea el archivo, línea, clase o función de donde se ejecuta
 * dicha función
 *
 * @return bool
 */
function backtrace()
{
	// Para seguir errores o ejecuciones de código
	$bt     = debug_backtrace();
	$caller = array_shift($bt);
	logger('BACKTRACE STARTS ----------------');
	logger(print_r($caller, true));
	logger('BACKTRACE ENDS -------------------');

	return true;
}

/**
 * Códificar a json de forma especial para prevenir errores en UTF8
 *
 * @param mixed $var
 * @return string
 */
function json_encode_utf8($var)
{
	return json_encode($var, JSON_UNESCAPED_UNICODE);
}

/**
 * Formateo de la hora en diferentes variantes
 * d M, Y
 * m Y
 * d m Y
 * mY
 * d M, Y time
 * @param string $date_string
 * @param string $type
 * @return string
 */
function format_date($date_string, $type = 'd M, Y')
{
	// Para versiones de PHP inferiores a 8.1.0 cuando se vuelve deprecada la función strftime()
	if (version_compare(phpversion(), '8.1.0', "<")) {
		setlocale(LC_ALL, "es_MX.UTF-8", "es_MX", "esp");

		// Fragmentos para fechas
		$anio      = strftime("%Y", strtotime($date_string));
		$mes       = strftime("%B", strtotime($date_string));
		$diames    = strftime("%d", strtotime($date_string));
		$diasemana = strftime("%A", strtotime($date_string));
		$dia       = strftime("%e", strtotime($date_string));
		$hora      = strftime("%H", strtotime($date_string));
		$minutos   = strftime("%M", strtotime($date_string));

		// Variantes para formar nuestra fecha
		$date = [
			'año'        => $anio,
			'mes'        => ucfirst($mes),
			'mes_corto'  => substr($mes, 0, 3),
			'dia'        => $dia,
			'dia_mes'    => $diames,
			'dia_semana' => ucfirst($diasemana),
			'hora'       => $hora,
			'minutos'    => $minutos,
			'tiempo'     => $hora . ':' . $minutos
		];

		switch ($type) {
			case 'd M, Y':
				return sprintf('%s de %s, %s', $date['dia'], $date['mes'], $date['año']);
			case 'm Y':
				return sprintf('%s %s', $date['mes'], $date['año']);
			case 'd m Y':
				return sprintf('%s %s %s', $date['dia'], $date['mes_corto'], $date['año']);
			case 'mY':
				return sprintf('%s, %s', ucfirst($date['mes_corto']), $date['año']);
			case 'MY':
				return sprintf('%s, %s', ucfirst($date['mes']), $date['año']);
			case 'd M, Y time':
				return $date['dia'] . ' de ' . $date['mes'] . ', ' . $date['año'] . ' a las ' . date('H:i A', strtotime($date_string));
			case 'time':
				return $date['tiempo'] . ' ' . date('A', strtotime($date_string));
			case 'date time':
				return $date['dia'] . '/' . $date['mes_corto'] . '/' . $date['año'] . ' ' . $date['tiempo'] . ' ' . date('A', strtotime($date_string));
			case 'short': //01/Nov/2019
				return sprintf('%s/%s/%s', $date['dia_mes'], ucfirst($date['mes_corto']), $date['año']);
			default:
				return sprintf('%s de %s, %s', $date['dia'], $date['mes'], $date['año']);
		}
	} else {
		$locale   = "es_MX";
		$timezone = date_default_timezone_get();

		// Validar si la extensión está activa:
		if (!extension_loaded('intl')) {
			throw new Exception('Debes activar la extensión "intl" en tu archivo php.ini');
		}

		$calendar = IntlDateFormatter::GREGORIAN;
		$pattern  = "";

		switch ($type) {
			case 'd M, Y':
				$pattern = "d 'de' MMMM, yyyy";
				break;

			case 'm Y':
				$pattern = "MMMM yyyy";
				break;

			case 'd m Y':
				$pattern = "dd MMMM yyyy";
				break;

			case 'mY':
				$pattern = "";
				break;

			case 'MY':
				$pattern = "";
				break;

			case 'd M, Y time':
				$pattern = "";
				break;

			case 'time':
				$pattern = "";
				break;

			case 'date time':
				$pattern = "";
				break;

			case 'short': //01/Nov/2019
				$pattern = "d'/'MMM'/'yyyy";
				break;

			default:
				$pattern = "EEEE, d 'de' MMMM, yyyy";
		}

		$fmt      = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL, $timezone, $calendar, $pattern);
		$res      = ucfirst(datefmt_format($fmt, strtotime($date_string)));

		// Debugging
		//echo datefmt_get_error_message($fmt);

		return $res;
	}
}

/**
 * Sanitiza un valor ingresado por usuario
 *
 * @param string $str
 * @param boolean $cleanhtml
 * @return string
 */
function clean($str, $cleanhtml = false)
{
	$str = @trim(@rtrim($str));
	$str = filter_var($str, FILTER_UNSAFE_RAW);

	if ($cleanhtml === true) {
		return htmlspecialchars($str);
	}

	return $str;
}

/**
 * Reconstruye un array de archivos posteados
 *
 * @param array $files
 * @return array|bool
 */
function arrenge_posted_files($files)
{
	if (empty($files)) {
		return false;
	}

	foreach ($files['error'] as $err) {
		if (intval($err) === 4) {
			return false;
		}
	}

	$file_ary   = array();
	$file_count = (is_array($files)) ? count($files['name']) : 1;
	$file_keys  = array_keys($files);

	for ($i = 0; $i < $file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $files[$key][$i];
		}
	}

	return $file_ary;
}

/**
 * Genera un string o password
 *
 * @param integer $length
 * @param string $type
 * @return string
 */
function random_password($length = 8, $type = 'default')
{
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

	if ($type === 'numeric') {
		$alphabet = '1234567890';
	}

	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

	for ($i = 0; $i < $length; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}

	return str_shuffle(implode($pass)); //turn the array into a string
}

/**
 * Genera un string o password
 *
 * @param integer $length
 * @return int
 */
function random_number(int $length = 8)
{
	$alphabet    = '1234567890';
	$pass        = []; //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

	for ($i = 0; $i < $length; $i++) {
		$n      = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}

	return (int) str_shuffle(implode($pass)); //turn the array into a string
}

/**
 * Agregar ellipsis a un string
 *
 * @param string $string
 * @param integer $lng
 * @return string
 */
function add_ellipsis($string, $lng = 100)
{
	if (!is_integer($lng)) {
		$lng = 100;
	}

	$output = strlen($string) > $lng ? mb_substr($string, 0, $lng, 'UTF-8') . '...' : $string;
	return $output;
}

/**
 * Devuelve la IP del cliente actual
 *
 * @return string
 */
function get_user_ip()
{
	$ipaddress = '';
	if (getenv('HTTP_CLIENT_IP'))
		$ipaddress = getenv('HTTP_CLIENT_IP');
	else if (getenv('HTTP_X_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	else if (getenv('HTTP_X_FORWARDED'))
		$ipaddress = getenv('HTTP_X_FORWARDED');
	else if (getenv('HTTP_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	else if (getenv('HTTP_FORWARDED'))
		$ipaddress = getenv('HTTP_FORWARDED');
	else if (getenv('REMOTE_ADDR'))
		$ipaddress = getenv('REMOTE_ADDR');
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

/**
 * Devuelve el sistema operativo del cliente
 *
 * @return string
 */
function get_user_os()
{
	if (isset($_SERVER)) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
	} else {
		global $HTTP_SERVER_VARS;
		if (isset($HTTP_SERVER_VARS)) {
			$agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
		} else {
			global $HTTP_USER_AGENT;
			$agent = $HTTP_USER_AGENT;
		}
	}
	$ros[] = array('Windows XP', 'Windows XP');
	$ros[] = array('Windows NT 5.1|Windows NT5.1)', 'Windows XP');
	$ros[] = array('Windows 2000', 'Windows 2000');
	$ros[] = array('Windows NT 5.0', 'Windows 2000');
	$ros[] = array('Windows NT 4.0|WinNT4.0', 'Windows NT');
	$ros[] = array('Windows NT 5.2', 'Windows Server 2003');
	$ros[] = array('Windows NT 6.0', 'Windows Vista');
	$ros[] = array('Windows NT 7.0', 'Windows 7');
	$ros[] = array('Windows CE', 'Windows CE');
	$ros[] = array('(media center pc).([0-9]{1,2}\.[0-9]{1,2})', 'Windows Media Center');
	$ros[] = array('(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows');
	$ros[] = array('(win)([0-9]{2})', 'Windows');
	$ros[] = array('(windows)([0-9x]{2})', 'Windows');
	$ros[] = array('Windows ME', 'Windows ME');
	$ros[] = array('Win 9x 4.90', 'Windows ME');
	$ros[] = array('Windows 98|Win98', 'Windows 98');
	$ros[] = array('Windows 95', 'Windows 95');
	$ros[] = array('(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows');
	$ros[] = array('win32', 'Windows');
	$ros[] = array('(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java');
	$ros[] = array('(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris');
	$ros[] = array('dos x86', 'DOS');
	$ros[] = array('unix', 'Unix');
	$ros[] = array('Mac OS X', 'Mac OS X');
	$ros[] = array('Mac_PowerPC', 'Macintosh PowerPC');
	$ros[] = array('(mac|Macintosh)', 'Mac OS');
	$ros[] = array('(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS');
	$ros[] = array('(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS');
	$ros[] = array('(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS');
	$ros[] = array('os/2', 'OS/2');
	$ros[] = array('freebsd', 'FreeBSD');
	$ros[] = array('openbsd', 'OpenBSD');
	$ros[] = array('netbsd', 'NetBSD');
	$ros[] = array('irix', 'IRIX');
	$ros[] = array('plan9', 'Plan9');
	$ros[] = array('osf', 'OSF');
	$ros[] = array('aix', 'AIX');
	$ros[] = array('GNU Hurd', 'GNU Hurd');
	$ros[] = array('(fedora)', 'Linux - Fedora');
	$ros[] = array('(kubuntu)', 'Linux - Kubuntu');
	$ros[] = array('(ubuntu)', 'Linux - Ubuntu');
	$ros[] = array('(debian)', 'Linux - Debian');
	$ros[] = array('(CentOS)', 'Linux - CentOS');
	$ros[] = array('(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - Mandriva');
	$ros[] = array('(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - SUSE');
	$ros[] = array('(Dropline)', 'Linux - Slackware (Dropline GNOME)');
	$ros[] = array('(ASPLinux)', 'Linux - ASPLinux');
	$ros[] = array('(Red Hat)', 'Linux - Red Hat');
	$ros[] = array('(linux)', 'Linux');
	$ros[] = array('(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS');
	$ros[] = array('amiga-aweb', 'AmigaOS');
	$ros[] = array('amiga', 'Amiga');
	$ros[] = array('AvantGo', 'PalmOS');
	$ros[] = array('[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux');
	$ros[] = array('(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV');
	$ros[] = array('Dreamcast', 'Dreamcast OS');
	$ros[] = array('GetRight', 'Windows');
	$ros[] = array('go!zilla', 'Windows');
	$ros[] = array('gozilla', 'Windows');
	$ros[] = array('gulliver', 'Windows');
	$ros[] = array('ia archiver', 'Windows');
	$ros[] = array('NetPositive', 'Windows');
	$ros[] = array('mass downloader', 'Windows');
	$ros[] = array('microsoft', 'Windows');
	$ros[] = array('offline explorer', 'Windows');
	$ros[] = array('teleport', 'Windows');
	$ros[] = array('web downloader', 'Windows');
	$ros[] = array('webcapture', 'Windows');
	$ros[] = array('webcollage', 'Windows');
	$ros[] = array('webcopier', 'Windows');
	$ros[] = array('webstripper', 'Windows');
	$ros[] = array('webzip', 'Windows');
	$ros[] = array('wget', 'Windows');
	$ros[] = array('Java', 'Unknown');
	$ros[] = array('flashget', 'Windows');
	$ros[] = array('(PHP)/([0-9]{1,2}.[0-9]{1,2})', 'PHP');
	$ros[] = array('MS FrontPage', 'Windows');
	$ros[] = array('(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows');
	$ros[] = array('(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows');
	$ros[] = array('libwww-perl', 'Unix');
	$ros[] = array('UP.Browser', 'Windows CE');
	$ros[] = array('NetAnts', 'Windows');
	$file = count($ros);
	$os = '';
	for ($n = 0; $n < $file; $n++) {
		if (@preg_match('/' . $ros[$n][0] . '/i', $agent, $name)) {
			$os = @$ros[$n][1] . ' ' . @$name[2];
			break;
		}
	}
	return trim($os);
}

/**
 * Devuelve el navegador del cliente
 *
 * @return string
 */
function get_user_browser()
{
	$user_agent = (isset($_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : NULL);

	$browser       = "Unknown Browser";

	$browser_array = array(
		'/msie/i'      => 'Internet Explorer',
		'/firefox/i'   => 'Firefox',
		'/safari/i'    => 'Safari',
		'/chrome/i'    => 'Chrome',
		'/edge/i'      => 'Edge',
		'/opera/i'     => 'Opera',
		'/netscape/i'  => 'Netscape',
		'/maxthon/i'   => 'Maxthon',
		'/konqueror/i' => 'Konqueror',
		'/mobile/i'    => 'Handheld Browser'
	);

	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser = $value;
		}
	}

	return $browser;
}

/**
 * Inserta campos htlm en un formulario
 *
 * @return string
 */
function insert_inputs()
{
	$output = '';

	if (isset($_POST['redirect_to'])) {
		$location = $_POST['redirect_to'];
	} else if (isset($_GET['redirect_to'])) {
		$location = $_GET['redirect_to'];
	} else {
		$location = CUR_PAGE;
	}

	$output .= '<input type="hidden" name="redirect_to" value="' . $location . '" required>';
	$output .= '<input type="hidden" name="timecheck" value="' . time() . '" required>';
	$output .= '<input type="hidden" name="csrf" value="' . CSRF_TOKEN . '" required>';

	return $output;
}

/**
 * Genera un nombre de archivo random
 *
 * @param integer $size
 * @param integer $span
 * @return string
 */
function generate_filename($size = 12, $span = 3)
{
	if (!is_integer($size)) {
		$size = 6;
	}

	$name = '';
	for ($i = 0; $i < $span; $i++) {
		$name .= random_password($size) . '-';
	}

	$name = rtrim($name, '-');
	return strtolower($name);
}

/**
 * Formatea el tamaño de un archivo
 *
 * @param float $size
 * @param integer $precision
 * @return string
 */
function filesize_formatter($size, $precision = 1)
{
	$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$step = 1024;
	$i = 0;
	while (($size / $step) > 0.9) {
		$size = $size / $step;
		$i++;
	}
	return round($size, $precision) . $units[$i];
}

/**
 * Arregla las diagonales invertidas de una URL
 *
 * @param string $url
 * @return string
 */
function fix_url($url)
{
	return str_replace('\\', '/', $url);
}

/**
 * Regresa el valor de sesión o un index en especial
 *
 * @param string $key
 * @return mixed|false
 */
function get_session($key = null)
{
	if ($key === null) {
		return $_SESSION;
	}

	// Si es un array, deberá ir separado con punto: ejemplo usuario.reportes
	if (strpos($key, ".") !== false) {
		$array = explode('.', $key);
		$lvls  = count($array);

		for ($i = 0; $i < $lvls; $i++) {
			if (!isset($_SESSION[$array[$i]])) {
				return false;
			}
		}
	}

	// Si es una clave directa sin subniveles
	return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
}

/**
 * Guarda en sesión un valor y un index
 *
 * @param string $k
 * @param mixed $v
 * @return bool
 */
function set_session($key, $value)
{
	$_SESSION[$key] = $value;
	return true;
}

/**
 * Envía un correo electrónico usando PHPMailer
 *
 * @param string $from
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $alt
 * @param string $bcc
 * @param string $reply_to
 * @param array $attachments
 * @return mixed
 */
function send_email($from, $to, $subject, $body, $alt = null, $bcc = null, $reply_to = null, $attachments = [])
{
	try {
		$mail     = new PHPMailer(PHPMAILER_EXCEPTIONS); // Para desactivar Excepciones pasar false al constructor
		$template = PHPMAILER_TEMPLATE;

		// Conexión SMTP y settings del servidor -- configurable en settings.php
		if (defined('PHPMAILER_SMTP') && PHPMAILER_SMTP === true) {
			$mail->isSMTP();
			if (PHPMAILER_DEBUG === true) {
				$mail->SMTPDebug  = SMTP::DEBUG_SERVER;        //Enable verbose debug output
			}

			$mail->SMTPAuth   = is_bool(PHPMAILER_AUTH) ? PHPMAILER_AUTH : true;  //Enable SMTP authentication
			$mail->Host       = PHPMAILER_HOST;                                   //Set the SMTP server to send through
			$mail->Username   = PHPMAILER_USERNAME;                               //SMTP username
			$mail->Password   = PHPMAILER_PASSWORD;                               //SMTP password
			$mail->SMTPSecure = PHPMAILER_SECURITY === 'ssl' ?
				PHPMailer::ENCRYPTION_SMTPS :
				PHPMailer::ENCRYPTION_STARTTLS;                                       //Enable implicit TLS encryption
			$mail->Port       = PHPMAILER_PORT;                                   // Puerto de conexión
		}

		// Charset del contenido
		$mail->CharSet = 'UTF-8';

		// Remitente
		$mail->setFrom($from, get_sitename());

		// Destinatario
		$mail->addAddress($to);

		if ($reply_to != null) {
			$mail->addReplyTo($reply_to);
		}

		if ($bcc != null) {
			$mail->addBCC($bcc);
		}

		// Attachments
		if (!empty($attachments)) {
			foreach ($attachments as $file) {
				if (!is_file($file)) {
					continue;
				}

				$mail->addAttachment($file);
			}
		}

		// Contenido
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = get_module($template, ['alt' => $alt, 'body' => $body, 'subject' => $subject]);
		$mail->AltBody = $alt;

		// Enviar el correo electrónico
		$mail->send();
		return true;
	} catch (EmailException $e) {
		throw new Exception($e->getMessage());
	}
}

/**
 * Muestra en pantalla los valores pasados
 *
 * @param mixed $data
 * @return void
 */
function debug($data, $var_dump_mode = false)
{
	if ($var_dump_mode === false) {
		echo '<pre>';
		if (is_array($data) || is_object($data)) {
			print_r($data);
		} else {
			echo $data;
		}
		echo '</pre>';
	} else {
		var_dump($data);
	}
}

/**
 * Creación de un token de 32 caractores por defecto
 *
 * @param integer $length
 * @return string
 */
function generate_token($length = 32)
{
	if (function_exists('random_bytes')) {
		$token = bin2hex(random_bytes($length));
	} elseif (function_exists('openssl_random_pseudo_bytes')) {
		$token = bin2hex(openssl_random_pseudo_bytes($length));
	} else {
		$token = '';
		for ($i = 0; $i < $length; $i++) {
			$token .= dechex(mt_rand(0, 15));
		}
	}

	return $token;
}

/**
 * Genera una key alfanúmerica con hash md5
 * con longitud de 30 caracteres
 * ejemplo:
 * 8042a4-a3bcd4-08d1e1-9596d6-24ae57
 *
 * @return string
 */
function generate_key()
{
	$key = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));

	return $key;
}

/**
 * Valida los parámetros pasados en POST
 *
 * @param array $required_params
 * @param array $posted_data
 * @return bool
 */
function check_posted_data($required_params = [], $posted_data = [])
{
	$posted_data = isset($_POST) ? $_POST : $posted_data;

	if (!is_array($required_params)) {
		return false;
	}

	if (empty($required_params) || empty($posted_data)) {
		return false;
	}

	// Keys necesarios en toda petición
	/**
	 * @deprecated 1.1.4
	 */
	$required = count($required_params);
	$found    = 0;

	foreach ($posted_data as $k => $v) {
		if (in_array($k, $required_params)) {
			$found++;
		}
	}

	if ($found !== $required) {
		return false;
	}

	return true;
}

/**
 * Valida parámetros ingresados en la URL como GET
 *
 * @param array $required_params
 * @param array $get_data
 * @return bool
 */
function check_get_data($required_params = [], $get_data = [])
{
	$get_data = isset($_GET) ? $_GET : $get_data;

	if (!is_array($required_params)) {
		return false;
	}

	if (empty($required_params) || empty($get_data)) {
		return false;
	}

	// Keys necesarios en toda petición
	/**
	 * @deprecated 1.1.4
	 */
	$required = count($required_params);
	$found    = 0;

	foreach ($get_data as $k => $v) {
		if (in_array($k, $required_params)) {
			$found++;
		}
	}

	if ($found !== $required) {
		return false;
	}

	return true;
}

/**
 * Agrega un tooltip con más información definida como string de Bootstrap 5
 *
 * @param string $content El contenido de texto del tooltip
 * @param string $color El color del icono del tooltip
 * @param string $icon La clase para el icono de fontawesome 5
 * @return string
 */
function more_info($content, $color = 'text-info', $icon = 'fas fa-exclamation-circle')
{
	$str    = clean($content);
	$output = '<span class="%s" %s><i class="%s"></i></span>';
	return sprintf($output, $color, tooltip($content), $icon);
}

/**
 * Agrega un placeholder a un campo input
 *
 * @param string $string
 * @return string
 */
function placeholder($string = 'Lorem ipsum')
{
	return sprintf('placeholder="%s"', $string);
}

/**
 * Agrega un tooltip en plantalla de Bootstrap 5
 *
 * @param string $title
 * @return string|bool
 */
function tooltip($title = null)
{
	if ($title == null) {
		return false;
	}

	return 'data-bs-toggle="tooltip" title="' . $title . '"';
}

/**
 * Genera un menú dinámico con base a los links pasados
 *
 * @param array $links
 * @param string $active
 * @return string
 */
function create_menu($links, $slug_active = 'home')
{
	$output = '';
	$output .= '<ul class="nav flex-column">';
	foreach ($links as $link) {
		if ($slug_active === $link['slug']) {
			$output .=
				sprintf(
					'<li class="nav-item">
        <a class="nav-link active" href="%s">
          <span data-feather="%s"></span>
          %s
        </a>
        </li>',
					$link['url'],
					$link['icon'],
					$link['title']
				);
		} else {
			$output .=
				sprintf(
					'<li class="nav-item">
        <a class="nav-link" href="%s">
          <span data-feather="%s"></span>
          %s
        </a>
        </li>',
					$link['url'],
					$link['icon'],
					$link['title']
				);
		}
	}
	$output .= '</ul>';

	return $output;
}

/**
 * Función para cargar el url de nuestro asset logotipo de bee framework
 *
 * @return string
 */
function get_bee_logo()
{
	$default_logo = BEE_LOGO;
	$dummy_logo   = 'https://via.placeholder.com/150x60';

	if (!is_file(IMAGES_PATH . $default_logo)) {
		return $dummy_logo;
	}

	return IMAGES . $default_logo;
}

/**
 * Función para cargar el url de nuestro asset logotipo del sitio
 *
 * @return string
 */
function get_logo()
{
	$default_logo = SITE_LOGO;
	$dummy_logo   = 'https://via.placeholder.com/150x60';

	if (!is_file(IMAGES_PATH . $default_logo)) {
		return $dummy_logo;
	}

	return IMAGES . $default_logo;
}

/**
 * Regresa el favicon del sitio con base 
 * al archivo definido en la función
 * por defecto el nombre de archivo es favicon.ico y se encuentra en la carpeta favicon
 *
 * @return mixed
 */
function get_favicon()
{
	$path        = FAVICON; // path del archivo favicon
	$favicon     = SITE_FAVICON; // nombre del archivo favicon
	$type        = '';
	$href        = '';
	$placeholder = '<link rel="icon" type="%s" href="%s">';

	switch (pathinfo($path . $favicon, PATHINFO_EXTENSION)) {
		case 'ico':
			$type = 'image/vnd.microsoft.icon';
			$href = $path . $favicon;
			break;

		case 'png':
			$type = 'image/png';
			$href = $path . $favicon;
			break;

		case 'gif':
			$type = 'image/gif';
			$href = $path . $favicon;
			break;

		case 'svg':
			$type = 'image/svg+xml';
			$href = $path . $favicon;
			break;

		case 'jpg':
		case 'jpeg':
			$type = 'image/jpg';
			$href = $path . $favicon;
			break;

		default:
			return false;
	}

	return sprintf($placeholder, $type, $href);
}

/**
 * Carga y regresa un valor determinao de la información del usuario
 * guardada en la variable de sesión actual
 * @version 1.5.5
 *
 * @param string $key Es el nombre de la columna en la base de datos
 * @return array|false
 */
function get_user($key = null)
{
	global $Bee_User; // Información persistente del usuario

	if (!isset($_SESSION['user_session'])) return false;

	$session = $_SESSION['user_session']; // información de la sesión del usuario actual, regresará siempre falso si no hay dicha sesión

	if (!isset($session['user']) || empty($session['user'])) return false;

	/**
	 * Se insertaba la información en sesión lo cual generaba un problema ya que solo se recargaba dicha información
	 * al volver iniciar la sesión, es decir aunque se actualizara información del usuario en la base de datos
	 * la información actual registrada en su sesión, seguiría siendo igual hasta que cerrara sesión y volviera a ingresar
	 * con este ajuste usando el objeto global Bee_User, cargamos la información que es registrada cada que carga la página.
	 * @version 1.5.5
	 */
	// $user = $session['user']; // información de la base de datos o directamente insertada del usuario
	$user = $Bee_User;

	// Regresa la información del usuario o de la clave pasada
	return $key === null ? $user : (isset($user[$key]) ? $user[$key] : false);
}

/**
 * Determina si el sistema está en modo demostración o no
 * para limitar el acceso o la interacción de los usuarios y funcionalidades
 *
 * @return boolean
 */
function is_demo()
{
	return IS_DEMO;
}

/**
 * Función para validar si el sistema es una demostración
 * y guardar una notificación flash y redirigir al usuario
 * de así solicitarlo
 * @return bool
 */
function check_if_demo($flash = true, $redirect = true)
{
	$demo = is_demo();

	if ($demo == false) return false;

	if ($flash == true) {
		Flasher::new(sprintf('No disponible en la versión de demostración de %s.', get_sitename()), 'danger');
	}

	if ($redirect == true) {
		Redirect::back();
	}

	return true;
}

/**
 * Para registrar una hoja de estilos de forma manual
 *
 * @param array $stylesheets
 * @param string $comment
 * @return bool
 */
function register_styles($stylesheets, $comment = null)
{
	global $Bee_Styles;

	$Bee_Styles[] =
		[
			'comment' => (!empty($comment) ? $comment : null),
			'files'   => $stylesheets
		];

	return true;
}

/**
 * Para registrar uno o más scripts de forma manual
 *
 * @param array $scripts
 * @param string $comment
 * @return bool
 */
function register_scripts($scripts, $comment = null)
{
	global $Bee_Scripts;

	$Bee_Scripts[] =
		[
			'comment' => (!empty($comment) ? $comment : null),
			'files'   => $scripts
		];

	return true;
}

/**
 * Carga los estilos registrados de forma manual
 * por la función register_styles()
 *
 * @return string
 */
function load_styles()
{
	global $Bee_Styles;
	$output = '';

	if (empty($Bee_Styles)) {
		return $output;
	}

	// Iterar sobre cada elemento registrado
	foreach (json_decode(json_encode($Bee_Styles)) as $css) {
		if ($css->comment) {
			$output .= '<!-- ' . $css->comment . ' -->' . "\n";
		}

		// Iterar sobre cada path de archivo registrado
		foreach ($css->files as $f) {
			$output .= "\t" . '<link rel="stylesheet" href="' . $f . '" >' . "\n";
		}
	}

	return $output;
}

/**
 * Carga los scrips registrados de forma manual
 * por la función register_scripts()
 *
 * @return string
 */
function load_scripts()
{
	global $Bee_Scripts;
	$output = '';

	if (empty($Bee_Scripts)) {
		return $output;
	}

	// Itera sobre todos los elementos registrados
	foreach (json_decode(json_encode($Bee_Scripts)) as $js) {
		if ($js->comment) {
			$output .= '<!-- ' . $js->comment . ' -->' . "\n";
		}

		// Itera sobre todos los paths registrados
		foreach ($js->files as $f) {
			$output .= '<script src="' . $f . '" type="text/javascript"></script>' . "\n";
		}
	}

	return $output;
}

/**
 * Registar un nuevo valor para el objeto Bee
 * insertado en el pie del sitio como objeto para
 * acceder a los parámetros de forma sencilla
 *
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function register_to_bee_obj($key, $value)
{
	global $Bee_Object;

	/**
	 * Formateo del key en caso de no ser válido para
	 * javascript
	 * @since 1.1.4
	 */
	$key = str_replace([' ', '-'], '_', $key);

	if (is_array($value) || is_object($value)) {
		$Bee_Object[$key] = $value;
	} else {
		$Bee_Object[$key] = clean($value);
	}

	return true;
}

/**
 * Carga el objeto Bee registrado y todos sus valores
 * por defecto y personalizados
 *
 * @return string
 */
function load_bee_obj()
{
	global $Bee_Object;
	$output = '';

	if (empty($Bee_Object)) {
		return $output;
	}

	$output = '<script>var Bee = %s </script>';

	return sprintf($output, json_encode_utf8($Bee_Object));
}

/**
 * Registra los parámetro por defecto de Bee
 *
 * @return array
 */
function bee_obj_default_config()
{
	$options =
		[
			'sitename'      => get_sitename(),
			'version'       => get_version(),
			'bee_name'      => get_bee_name(),
			'bee_version'   => get_bee_version(),
			'csrf'          => CSRF_TOKEN,
			'url'           => URL,
			'cur_page'      => CUR_PAGE,
			'is_local'      => IS_LOCAL,
			'is_demo'       => IS_DEMO,
			'basepath'      => BASEPATH,
			'sandbox'       => SANDBOX,
			'port'          => PORT,
			'request_uri'   => REQUEST_URI,
			'assets'        => ASSETS,
			'images'        => IMAGES,
			'uploaded'      => UPLOADED,
			'php_version'   => phpversion(),
			'css_framework' => CSS_FRAMEWORK,
			'toastr'        => TOASTR,
			'axios'         => AXIOS,
			'sweetalert2'   => SWEETALERT2,
			'waitme'        => WAITME,
			'lightbox'      => LIGHTBOX,
			'vuejs'         => VUEJS,
			'public_key'    => get_bee_api_public_key(),
			'private_key'   => get_bee_api_private_key()
		];

	return $options;
}

/**
 * Registra los scripts y estilos del editor textual Summernote
 *
 * @return bool
 */
function use_summernote()
{
	register_styles(['https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-lite.css'], 'Summernote');
	register_scripts(['https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-lite.js'], 'Summernote');
	return true;
}

/**
 * Carga un recurso o imagen que esté directamente
 * en la carpeta de imágenes de bee framework
 *
 * @param string $filename
 * @return string
 */
function get_image($filename)
{
	if (!is_file(IMAGES_PATH . $filename)) {
		return IMAGES . 'broken.png';
	}

	return IMAGES . $filename;
}

/**
 * Carga un asset que ha sido subido al sistema en la carpeta
 * de uploads definida por bee framework
 *
 * @param string $filename
 * @return string
 */
function get_uploaded_image($filename)
{
	if (!is_file(UPLOADS . $filename)) {
		return IMAGES . 'broken.png';
	}

	return UPLOADED . $filename;
}

/**
 * Función para subir de forma segura al servidor un adjungo / imagen
 *
 * @param string $file_field
 * @param boolean $check_image
 * @param boolean $random_name
 * @return mixed
 */
function upload_image($file_field = null, $check_image = false, $random_name = false)
{
	// Path para subir el archivo
	$path = UPLOADS;

	// Tamaño máximo en bytes
	$max_size = 1000000;

	// Lista blanca de extensiones permitidas
	$whitelist_ext = array('jpeg', 'jpg', 'png', 'gif');

	// Tipos de archivo permitidos en lista blanca
	$whitelist_type = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

	// Validación
	// Para guardar cualquier error presente en el proceso
	$out = array('error' => null);

	if (!$file_field) {
		throw new Exception('Por favor específica un campo de archivo válido.', 1);
	}

	if (!is_dir($path)) {
		throw new Exception('Por favor específica una ruta de guardado válida.', 1);
	}

	// Si no se sube un archivo
	if ((!empty($_FILES[$file_field])) || ($_FILES[$file_field]['error'] !== 0)) {
		throw new Exception('Ningún archivo seleccionado para subir.', 1);
	}

	// Nombre del archivo
	$file_info = pathinfo($_FILES[$file_field]['name']);
	$name      = $file_info['filename'];
	$ext       = $file_info['extension'];

	// Verificar extensión
	if (!in_array($ext, $whitelist_ext)) {
		throw new Exception('La extensión no es válida o permitida.', 1);
	}

	// Verificar tipo de archivo
	if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
		throw new Exception('El tipo de archivo no es válido o permitido.', 1);
	}

	// Verificar tamaño
	if ($_FILES[$file_field]["size"] > $max_size) {
		throw new Exception('El tamaño del archivo es demasiado grande.', 1);
	}

	// Verificar si es una imagen válida
	if ($check_image === true) {
		if (!getimagesize($_FILES[$file_field]['tmp_name'])) {
			throw new Exception('El archivo seleccionado no es una imagen válida.', 1);
		}
	}

	// Crear nombre random de archivo
	if ($random_name === true) {
		$newname = generate_filename() . '.' . $ext;
	} else {
		$newname = $name . '.' . $ext;
	}

	// Verificar si el nombre ya existe en el servidor
	if (file_exists($path . $newname)) {
		throw new Exception('Un archivo con el mismo nombre ya existe en el servidor.', 1);
	}

	// Guardando en el servidor
	if (move_uploaded_file($_FILES[$file_field]['tmp_name'], $path . $newname) === false) {
		throw new Exception('Hubo un error en el servidor, intenta más tarde.', 1);
	}

	// Se ha subido con éxito el archivo y se ha guardado
	$out['filepath'] = $path;
	$out['filename'] = $newname;
	$out['file']     = $path . $newname;
	return $out;
}

/**
 * Validación del sistema en
 * desarrollo local
 *
 * @return boolean
 */
function is_local()
{
	return (IS_LOCAL === true);
}

/**
 * Validación del sistema si está en modo de pruebas
 * para transacciones o pasarelas de pago
 */
function is_sandbox()
{
	return SANDBOX === true;
}

/**
 * Carga las hojas de estilos con CDN del framework css a utilizar
 * definido en settings.php
 *
 * @return mixed
 */
function get_css_framework()
{
	if (!defined('CSS_FRAMEWORK')) {
		return false;
	}

	$framework   = CSS_FRAMEWORK;
	$placeholder = '<link rel="stylesheet" href="%s">';
	$bs_themes   = CSS . 'bs_themes/';
	$cdn         = null;

	switch ($framework) {
		case 'bl':
			$cdn = 'https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css';
			break;

		case 'fn':
			$cdn = 'https://cdn.jsdelivr.net/npm/foundation-sites@6.6.3/dist/css/foundation.min.css';
			break;

		case 'bs_lux':
			$cdn = $bs_themes . 'lux.min.css';
			break;

		case 'bs_lumen':
			$cdn = $bs_themes . 'lumen.min.css';
			break;

		case 'bs_litera':
			$cdn = $bs_themes . 'litera.min.css';
			break;

		case 'bs_vapor':
			$cdn = $bs_themes . 'vapor.min.css';
			break;

		case 'bs_zephyr':
			$cdn = $bs_themes . 'zephyr.min.css';
			break;

		case 'bs':
		case 'bs5':
		default:
			$cdn = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css';
			break;
	}

	return sprintf($placeholder, $cdn);
}

/**
 * Carga los scripts requeridos para el framework css a utilizar
 * definido en settings.php
 * @return mixed
 */
function get_css_framework_scripts()
{
	if (!defined('CSS_FRAMEWORK')) {
		return false;
	}

	$framework   = CSS_FRAMEWORK;
	$placeholder = '<script src="%s"></script>';
	$cdn         = null;

	switch ($framework) {

		case 'fn':
			$cdn = 'https://cdn.jsdelivr.net/npm/foundation-sites@6.6.3/dist/js/foundation.min.js';
			break;

		case 'bl':
			return ''; // Bulma no cuenta con scripts

		case 'bs_litera':
		case 'bs_lumen':
		case 'bs_lux':
		case 'bs_vapor':
		case 'bs_zephyr':
		case 'bs':
		case 'bs5':
		default:
			$cdn = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js';
			break;
	}

	return sprintf($placeholder, $cdn);
}

/**
 * Carga de jQuery solo de ser necesario
 * definido en settings.php
 *
 * @return mixed
 */
function get_jquery()
{
	if (!defined('JQUERY')) {
		return false;
	}

	$placeholder = '<script src="%s"></script>';
	$cdn         = 'https://code.jquery.com/jquery-3.6.0.min.js';

	return JQUERY === true ? sprintf($placeholder, $cdn) : '<!-- Desactivado en settings -->';
}

/**
 * Carga jQuery easing de ser necesario
 *
 * @return string
 */
function get_jquery_easing()
{
	if (!defined('JQUERY')) {
		return false;
	}

	$placeholder = '<script src="%s"></script>';
	$cdn         = 'https://cdn.jsdelivr.net/npm/jquery.easing@1.4.1/jquery.easing.min.js';

	return JQUERY === true ? sprintf($placeholder, $cdn) : '<!-- Desactivado en settings -->';
}

/**
 * Carga de Vuejs 3 solo de ser necesario
 * definido en settings.php
 *
 * @return string
 */
function get_vuejs($runtime = false)
{
	if (!defined('VUEJS')) {
		return false;
	}

	$placeholder = '<script src="%s"></script>';
	$cdn         = is_local() ? 'https://unpkg.com/vue@3/dist/vue.global.js' : ($runtime === true ? 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.0.11/vue.runtime.global.prod.js' : 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.0.11/vue.global.prod.js');

	return VUEJS === true ? sprintf($placeholder, $cdn) : '<!-- Desactivado en settings -->';
}

/**
 * Carga de Axios solo de ser necesario
 * definido en settings.php
 *
 * @return mixed
 */
function get_axios()
{
	if (!defined('AXIOS')) {
		return false;
	}

	$placeholder = '<script src="%s"></script>';
	$cdn         = 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';

	return AXIOS === true ? sprintf($placeholder, $cdn) : '<!-- Desactivado en settings -->';
}

/**
 * Carga de Toastr solo de ser necesario
 * definido en settings.php
 *
 * @return mixed
 */
function get_toastr($type = 'script')
{
	if (!defined('TOASTR')) {
		return false;
	}

	$disabled = '<!-- Desactivado en settings.php -->';

	if (TOASTR !== true) return $disabled;

	// Cabecera del sitio
	switch ($type) {
		case 'styles':
			$placeholder = '<link rel="stylesheet" href="%s"/>';
			$cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css';
			break;

		case 'script':
		default:
			$placeholder = '<script src="%s"></script>';
			$cdn         = '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js';
			break;
	}

	return sprintf($placeholder, $cdn);
}

/**
 * Carga de SweetAlert2 solo de ser necesario
 * definido en settings.php
 *
 * @return mixed
 */
function get_sweetalert2()
{
	if (!defined('SWEETALERT2')) {
		return false;
	}

	$placeholder = '<script src="%s"></script>';
	$cdn         = 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js';

	return SWEETALERT2 === true ? sprintf($placeholder, $cdn) : '<!-- Desactivado en settings -->';
}

/**
 * Carga de WaitMe solo de ser necesario
 * definido en settings.php
 *
 * @return mixed
 */
function get_waitMe($type = 'script')
{
	if (!defined('WAITME')) {
		return false;
	}

	$disabled = '<!-- Desactivado en settings.php -->';

	if (WAITME !== true) return $disabled;

	// Cabecera del sitio
	switch ($type) {
		case 'styles':
			$placeholder = '<link rel="stylesheet" href="%s"/>';
			$cdn         = PLUGINS . 'waitme/waitMe.min.css';
			break;

		case 'script':
		default:
			$placeholder = '<script src="%s"></script>';
			$cdn         = PLUGINS . 'waitme/waitMe.min.js';
			break;
	}

	return sprintf($placeholder, $cdn);
}

/**
 * Carga de Lightbox solo de ser necesario
 * definido en settings.php
 *
 * @return string
 */
function get_lightbox($type = 'script')
{
	if (!defined('LIGHTBOX')) {
		return false;
	}

	$disabled = '<!-- Desactivado en settings.php -->';

	if (LIGHTBOX !== true) return $disabled;

	// Cabecera del sitio
	switch ($type) {
		case 'styles':
			$placeholder = '<link rel="stylesheet" href="%s"/>';
			$cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css';
			break;

		case 'script':
		default:
			$placeholder = '<script src="%s"></script>';
			$cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js';
			break;
	}

	return sprintf($placeholder, $cdn);
}

/**
 * Regresa el CDN de fontawesome CSS versión 6
 *
 * @return string
 */
function get_fontawesome()
{
	$placeholder = '<link rel="stylesheet" href="%s"/>';
	$cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css';

	return sprintf($placeholder, $cdn);
}

/**
 * Muestra toda la información actual de Bee
 * y sus variables de configuración
 *
 * @return mixed
 */
function get_bee_info()
{
	$db   = Db::connect();
	$data =
		[
			'Título'               => sprintf('Bee framework %s', get_bee_version()),
			'Versión Bee'          => get_bee_version(),
			'Versión PHP'          => phpversion(),
			'Versión MySQL'        => $db->getAttribute(PDO::ATTR_SERVER_VERSION),
			'Charset'              => SITE_CHARSET,
			'Lenguaje'             => SITE_LANG,
			'Entorno local'        => IS_LOCAL === true ? 'Si' : 'No',
			'Demostración'         => IS_DEMO === true ? 'Si' : 'No',
			'Sandbox'              => SANDBOX === true ? 'Si' : 'No',
			'URL del sitio'        => URL,
			'URL actual'           => CUR_PAGE,
			'Path base'            => BASEPATH,
			'Raíz'                 => ROOT,
			'App'                  => APP,
			'Templates'            => TEMPLATES,
			'Configuración'        => CONFIG,
			'Controladores'        => CONTROLLERS,
			'Modelos'              => MODELS,
			'Clases'               => CLASSES,
			'Funciones'            => FUNCTIONS,
			'Logs'                 => LOGS,
			'Includes'             => INCLUDES,
			'Módulos'              => MODULES,
			'Vistas'               => VIEWS,
			'Imágenes'             => IMAGES_PATH,
			'Subidas'              => UPLOADS,
			'Usando Prepros'       => PREPROS === true ? 'Si' : 'No',
			'Puerto Prepros'       => PORT,
			'URL de recursos'      => ASSETS,
			'URL de subidas'       => UPLOADED,
			'URL de imágenes'      => IMAGES,
			'Sal de seguridad'     => IS_LOCAL ? AUTH_SALT : '*****',
			'DB Engine (local)'    => LDB_ENGINE,
			'DB Host (local)'      => LDB_HOST,
			'DB Nombre (local)'    => LDB_NAME,
			'DB Usuario (local)'   => LDB_USER,
			'DB Charset (local)'   => LDB_CHARSET,
			'DB Engine'            => DB_ENGINE,
			'DB Host'              => IS_LOCAL ? DB_HOST : '***',
			'DB Nombre'            => IS_LOCAL ? DB_NAME : '***',
			'DB Usuario'           => IS_LOCAL ? DB_USER : '***',
			'DB Charset'           => DB_CHARSET,
			'Plantilla de correos' => PHPMAILER_TEMPLATE,
			'Nombre del sitio'     => SITE_NAME,
			'Versión del sitio'    => SITE_VERSION,
			'Favicon del sitio'    => SITE_FAVICON,
			'Logotipo del sitio'   => SITE_LOGO,
			'Google Maps'          => IS_LOCAL ? GMAPS : '***',
		];

	return get_module('bee/info', $data);
}

/**
 * Crear una nueva contraseña random o 
 * definida para usuario
 *
 * @param string $password
 * @return array
 */
function get_new_password($password = null)
{
	$password = $password === null ? random_password() : $password;

	return
	[
		'password' => $password,
		'hash'     => password_hash($password . AUTH_SALT, PASSWORD_BCRYPT)
	];
}

/**
 * Función estándar para realizar
 * un die de sitio con bee framework
 * muestra contenido html5 de forma más estética
 *
 * @param string $message
 * @param array $headers
 * @return mixed
 */
function bee_die(string $error, $headers = [])
{
	if (empty($headers)) {
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}

	$data =
	[
		'title' => 'Hubo un error',
		'error' => $error
	];

	$html = get_module('bee/generalError', $data);
	die($html);
}

/**
 * Nueva función para mostrar una vista especial para errores de conexión a la base de datos
 *
 * @param string $error
 * @return void
 */
function bee_db_die(string $error)
{
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	$data =
	[
		'title' => 'Error en la base de datos',
		'error' => $error
	];

	echo get_module('bee/dbError', $data);
	die();
}

/**
 * Determina si el framework va a trabajar o no con sesiones persistentes
 * basadas en Cookies en el exploral del usuario
 *
 * @return bool
 */
function persistent_session()
{
	if (!defined('BEE_COOKIES') || BEE_COOKIES !== true) {
		return false;
	}

	return true;
}

/**
 * Cargar y regresa todos los cookies del sitio
 *
 * @return array
 */
function get_all_cookies()
{
	$cookies = [];

	if (!isset($_COOKIE) || empty($_COOKIE)) {
		return $cookies;
	}

	// Iteramos entre todos los cookies guardados del sitio
	// para almacenarlos en una nueva variable
	foreach ($_COOKIE as $name => $value) {
		$cookies[$name] = $value;
	}

	return $cookies;
}

/**
 * Para volver a buscar y asignar los cookies
 * a nuestra global de cookies en caso de que
 * sea necesario
 *
 * @return bool
 */
function load_all_cookies()
{
	global $Bee_Cookies;

	$Bee_Cookies = get_all_cookies();

	return true;
}

/**
 * Creamos un cookie directamente
 * con base a los parámetros pasados
 *
 * @param array $cookies
 * @return bool
 */
function new_cookie($name, $value, $lifetime = null, $path = '', $domain = '')
{
	// Para prevenir cualquier error de ejecución
	// al ser enviadas ya las cabeceras del sitio
	if (headers_sent()) {
		return false;
	}

	// Valor por defecto de la duración del cookie
	$default  = 60 * 60 * 24; // 1 día por defecto si no existe la constante
	$lifetime = defined('BEE_COOKIE_LIFETIME') && $lifetime === null ? BEE_COOKIE_LIFETIME : (!is_integer($lifetime) ? $default : $lifetime);

	// Creamos el nuevo cookie
	setcookie($name, $value, time() + $lifetime, $path, $domain);

	return true;
}

/**
 * Carga la información de un cookie en caso de existir
 *
 * @param string $cookie
 * @return bool | true si existe | false si no
 */
function cookie_exists($cookie)
{
	return isset($_COOKIE[$cookie]);
}

/**
 * Borrar cookies en caso de existir,
 * se pasa el nombre de cada cookie como parámetro array
 *
 * @param array $cookies
 * @return bool
 */
function destroy_cookie($cookie, $path = '', $domain = '')
{
	global $Bee_Cookies;

	// Para prevenir cualquier error de ejecución
	// al ser enviadas ya las cabeceras del sitio
	if (headers_sent()) {
		return false;
	}

	// Verificamos que exista el cookie dentro de nuestra
	// global, si no existe entonces no existe el cookie en sí
	if (!isset($_COOKIE[$cookie])) {
		return false;
	}

	// Seteamos el cookie con un valor null y tiempo negativo para destruirlo
	setcookie($cookie, null, time() - 1000, $path, $domain);
	unset($Bee_Cookies[$cookie]);

	return true;
}

/**
 * Verifica si existe un determinado cookie creado
 *
 * @param string $cookie_name
 * @return mixed
 */
function get_cookie($cookie)
{
	return isset($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : false;
}

/**
 * Carga todos los mensajes configurados por defecto para
 * su uso en el sistema de Bee framework
 *
 * @return array
 */
function get_bee_default_messages()
{
	$messages =
		[
			'0'           => 'Acceso no autorizado.',
			'1'           => 'Acción no autorizada.',
			'2'           => 'Ocurrió un error, intenta más tarde.',
			'3'           => 'No pudimos procesar tu solicitud.',
			'added'       => 'Nuevo registro agregado con éxito.',
			'not_added'   => 'Hubo un problema al agregar el registro.',
			'updated'     => 'Registro actualizado con éxito.',
			'not_updated' => 'Hubo un problema al actualizar el registro.',
			'found'       => 'Registro encontrado con éxito.',
			'not_found'   => 'El registro no existe o ha sido borrado.',
			'deleted'     => 'Registro borrado con éxito.',
			'not_deleted' => 'Hubo un problema al borrar el registro.',
			'sent'        => 'Mensaje enviado con éxito.',
			'not_sent'    => 'Hubo un problema al enviar el mensaje.',
			'sent_to'     => 'Mensaje enviado con éxito a %s.',
			'not_sent_to' => 'Hubo un problema al enviar el mensaje a %s.',
			'auth'        => 'Debes iniciar sesión para continuar.',
			'expired'     => 'La sesión ha expirado, vuelve a ingresar por favor.',
			'm_params'    => 'Parámetros incompletos, acceso no autorizado.',
			'm_form'      => 'Campos incompletos, completa el formulario por favor.',
			'm_token'     => 'Token no encontrado o no válido, acceso no autorizado.'
		];

	return $messages;
}

/**
 * Carga todos los mensajes registrados en el
 * array de Bee_Messages del sistema bee framework
 *
 * @return array
 */
function get_all_bee_messages()
{
	global $Bee_Messages;

	return $Bee_Messages;
}

/**
 * Registra un nuevo mensaje al array
 * de mensajes para su uso en bee framework
 *
 * @param string $code
 * @param string $message
 * @return bool
 */
function register_bee_custom_message($code, $message)
{
	global $Bee_Messages;

	try {
		if (isset($Bee_Messages[$code])) {
			throw new Exception(sprintf('Ya existe el código de mensaje %s.', $code));
		}

		$Bee_Messages[$code] = $message;

		return true;
	} catch (Exception $e) {
		bee_die($e->getMessage());
	}
}

/**
 * Carga un mensaje de bee framework existente
 * en el array de la global Bee_Messages
 * 
 * OPCIONES ACTUALES
 * '0'           => 'Acceso no autorizado.'
 * '1'           => 'Acción no autorizada.'
 * '2'           => 'Ocurrió un error, intenta más tarde.'
 * '3'           => 'No pudimos procesar tu solicitud.'
 * 'added'       => 'Nuevo registro agregado con éxito.'
 * 'not_added'   => 'Hubo un problema al agregar el registro.'
 * 'updated'     => 'Registro actualizado con éxito.'
 * 'not_updated' => 'Hubo un problema al actualizar el registro.'
 * 'found'       => 'Registro encontrado con éxito.'
 * 'not_found'   => 'El registro no existe o ha sido borrado.'
 * 'deleted'     => 'Registro borrado con éxito.'
 * 'not_deleted' => 'Hubo un problema al borrar el registro.'
 * 'sent'        => 'Mensaje enviado con éxito.'
 * 'not_sent'    => 'Hubo un problema al enviar el mensaje.'
 * 'sent_to'     => 'Mensaje enviado con éxito a %s.'
 * 'not_sent_to' => 'Hubo un problema al enviar el mensaje a %s.'
 * 'auth'        => 'Debes iniciar sesión para continuar.'
 * 'expired'     => 'La sesión ha expirado, vuelve a ingresar por favor.'
 * 'm_params'    => 'Parámetros incompletos, acceso no autorizado.'
 * 'm_form'      => 'Campos incompletos, completa el formulario por favor.'
 * 'm_token'     => 'Token no encontrado o no válido, acceso no autorizado.' 
 * 
 * @param string $code
 * @return string
 */
function get_bee_message($code)
{
	global $Bee_Messages;

	$code = (string) $code;

	return isset($Bee_Messages[$code]) ? $Bee_Messages[$code] : '';
}

/**
 * Regresa la api key pública para consumir las rutas de la API
 *
 * @return string
 */
function get_bee_api_public_key()
{
	$name = 'API_PUBLIC_KEY';
	if (!defined($name)) {
		throw new Exception(sprintf('La constante %s no existe o no se ha definido en el sistema y es requerida para esta función.', $name));
	}

	$key = API_PUBLIC_KEY;
	return $key;
}

/**
 * Regresa la api key privada para consumir las rutas de la API
 *
 * @return string
 */
function get_bee_api_private_key()
{
	$name = 'API_PRIVATE_KEY';
	if (!defined($name)) {
		throw new Exception(sprintf('La constante %s no existe o no se ha definido en el sistema y es requerida para esta función.', $name));
	}

	$key = API_PRIVATE_KEY;
	return $key;
}

/**
 * Regresa true si es requerida autenticación con api keys para consumir los
 * recursos de la API de bee framework y la instancia actual
 *
 * @return bool
 */
function bee_api_authentication()
{
	$name = 'API_AUTH';
	if (!defined($name)) {
		throw new Exception(sprintf('La constante %s no existe o no se ha definido en el sistema y es requerida para esta función.', $name));
	}

	return (API_AUTH === true);
}

/**
 * Regresa una versión de un asset si estamos en desarrollo generará
 * una versión random para evitar el cache y cargar el archivo más reciente,
 * en producción cargará la versión cacheada
 *
 * @return string
 */
function get_asset_version()
{
	return is_local() ? time() : get_version();
}

/**
 * Valida si el string o texto es alfanumérico, puede o no tener espacios incluidos
 *
 * @param string $string
 * @param boolean $spaces
 * @return boolean
 */
function is_alphanumeric($string, $accept_spaces = false)
{
	if ($accept_spaces === true) {
		return preg_match("/^[\p{L} ]+$/u", clean($string)) === 1;
	}

	return preg_match("/^[a-zA-Z0-9]+$/", clean($string)) === 1;
}

/**
 * Valida si una variable es nula o está vacía, de ser así
 * regresa un em-dash para sustituir su espacio, denotando que no hay
 * información en dicho lugar
 *
 * @param mixed $value
 * @param string $placeholder
 * @return string
 */
function _e($value, string $placeholder = "—")
{
	if (empty($value) || !isset($value)) {
		return $placeholder;
	}

	return $value;
}

/**
 * Devuelve el metal pixel almacenado en la tabla
 * de opciones de la base de datos
 *
 * @return string
 */
function get_meta_pixel()
{
	$meta_pixel = get_option('fb-pixel');
	return $meta_pixel;
}

/**
 * Inyecta el código html para inicializar
 * el rastreo de meta pixel
 *
 * @return string
 */
function init_meta_pixel()
{
	$pixel  = get_meta_pixel();
	$output = '';

	if (empty($pixel)) return $output;

	$output .= '<!-- Meta Pixel Code -->';
	$output .=
		"<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '%1\$s');
    fbq('track', 'PageView');
  </script>";

	$output .= '<noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=%1$s&ev=PageView&noscript=1"
  /></noscript>';
	$output .= '<!-- End Meta Pixel Code -->';

	return sprintf($output, $pixel);
}

/**
 * Establece los valores de los og meta tags
 *
 * @param string $title
 * @param string $description
 * @param string $image
 * @param string $url
 * @param string $type
 * @return bool
 */
function set_page_og_meta_tags($title = null, $description = null, $image = null, $url = null, $type = 'article')
{
	global $OG_Title;
	global $OG_Description;
	global $OG_Url;
	global $OG_Image;
	global $OG_Type;

	$OG_Title       = $title === null ? get_sitename() : $title;
	$OG_Description = $description === null ? SITE_DESC : $description;
	$OG_Url         = $url === null ? CUR_PAGE : $url;
	$OG_Image       = $image === null ? IMAGES . 'og-bee-framework.png'  : $image;
	$OG_Type        = $type === 'article' ? $type : 'website';

	return true;
}

/**
 * Regresa el código html de los open graph meta tags
 * listos para ser insertados en el DOM
 *
 * @param string $title
 * @param string $description
 * @param string $image
 * @param string $url
 * @param boolean $article
 * @return string
 */
function get_page_og_meta_tags()
{
	global $OG_Title;
	global $OG_Description;
	global $OG_Url;
	global $OG_Image;
	global $OG_Type;

	$output = '';
	$output .= sprintf('<meta property="og:title" content="%s" />', $OG_Title);
	$output .= sprintf('<meta property="og:description" content="%s" />', $OG_Description);
	$output .= sprintf('<meta name="description" content="%s" />', $OG_Description);
	$output .= sprintf('<meta property="og:url" content="%s" />', $OG_Url);
	$output .= sprintf('<meta property="og:type" content="%s" />', $OG_Type);
	$output .= sprintf('<meta property="og:image" content="%s" />', $OG_Image);

	return $output;
}

/**
 * Sanitiza el input de usuario para prevenir cualquier código malicioso
 * al ser guardado en bases de datos o inyectado en el DOM
 *
 * @param string $input
 * @param integer $max_length
 * @return string
 */
function sanitize_input($input, $max_length = null)
{
	// Eliminamos espacios en blanco adicionales al inicio y al final del input.
	$sanitized_input = trim($input);

	// Eliminar etiquetas HTML y PHP del input.
	$sanitized_input = strip_tags($sanitized_input);

	// Utilizamos FILTER_SANITIZE_FULL_SPECIAL_CHARS para eliminar o codificar caracteres
	// especiales que podrían ser peligrosos o causar problemas de seguridad.
	//$sanitized_input = filter_var($sanitized_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$sanitized_input = filter_var($sanitized_input, FILTER_UNSAFE_RAW);

	// Si el input contiene una cadena mal formada de UTF-8, eliminamos los bytes inválidos.
	// Esto puede ayudar a prevenir ataques basados en UTF-8 malformado.
	//$sanitized_input = mb_convert_encoding($sanitized_input, 'UTF-8', 'UTF-8');

	// Otras medidas de seguridad adicionales, como limitar la longitud del input.
	if ($max_length !== null) {
		$max_length      = !is_integer($max_length) ? 100 : $max_length; // Define el máximo número de caracteres permitidos.
		$sanitized_input = substr($sanitized_input, 0, $max_length);
	}

	return $sanitized_input;
}

/**
 * Genera el hash de un string usando el algorítmo sha256
 *
 * @param string $data
 * @return string
 */
function hash_256($data)
{
	// Verificamos si el algoritmo SHA-256 está disponible
	if (in_array('sha256', hash_algos(), true)) {
		// Calculamos el hash SHA-256 y lo devolvemos
		return hash('sha256', $data);
	}

	// Si el algoritmo SHA-256 no está disponible, devolvemos un valor nulo, es poco probable que pase
	return null;
}

/**
 * Normaliza el formato de un string para envío seguro de información o almacenado
 *
 * @param string $input
 * @param boolean $remove_internal_spaces
 * @param boolean $remove_special_chars
 * @return string
 */
function normalize_string($input, $remove_internal_spaces = false, $remove_special_chars = false)
{
	// Convertimos a minúsculas y eliminamos espacios extras al inicio y final
	$normalized = trim(mb_strtolower($input, 'UTF-8'));

	// Opcionalmente, removemos espacios internos si se solicita
	if ($remove_internal_spaces) {
		$normalized = preg_replace('/\s+/', '', $normalized);
	}

	// Opcionalmente, removemos caracteres especiales si se solicita
	if ($remove_special_chars) {
		$normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
		$normalized = htmlspecialchars($normalized, ENT_QUOTES, 'UTF-8');
	}

	return $normalized;
}

/**
 * Regresa el valor de la constante BASEPATH
 *
 * @return string
 */
function get_basepath()
{
	return BASEPATH;
}

/**
 * Regresa el valor del lenguaje por defecto del sistema, configurado en settings.php
 *
 * @return string
 */
function get_site_lang()
{
	return SITE_LANG;
}

/**
 * Regresa el valor del charset por defecto del sistema, configurado en settings.php
 *
 * @return void
 */
function get_site_charset()
{
	return SITE_CHARSET;
}

/**
 * Obtiene la URL de referencia cuando alguien viene de alguna fuente a nuestro sistema
 *
 * @param string $fallbackUrl
 * @return string
 */
function get_referer_url($fallbackUrl = null)
{
	// TODO: Implementar al inicio de la carga del sitio para almacenarla y tenerla disponible
	
	// Verificar si el encabezado 'Referer' está presente
	if (isset($_SERVER['HTTP_REFERER'])) {
		// Obtener la URL de referencia
		return $_SERVER['HTTP_REFERER'];
	} else {
		return $fallbackUrl === null ? get_base_url() : $fallbackUrl;
	}
}

/**
 * Verifica si el usuario actual está loggeado o no
 *
 * @return boolean
 */
function is_logged()
{
	return Auth::validate();
}

/**
 * Imprime un bloque de código de forma segura cómo string
 *
 * @param string $snippet
 * @return string
 */
function code_block($snippet)
{
	$snippet = htmlentities($snippet);
	return "<pre class='code-block'><code>$snippet</code></pre>";
}

/**
 * Verifica si una dirección de correo electrónico
 * no es de un proveedor de correos electrónicos temporales
 *
 * @param string $email
 * @return boolean
 */
function is_temporary_email($email)
{
	// Lista de proveedores de correo temporal
	$temporaryDomains = [
		'guerrillamail.com', 'mailinator.com', 'sharklasers.com', 'getnada.com', 'fakeinbox.com',
		'dispostable.com', 'yopmail.com', 'tempmail.com', 'mailnesia.com', 'jetable.com',
		'mintemail.com', 'emailondeck.com', 'throwawaymail.com', 'spambog.com', 'mailexpire.com',
		'mailcatch.com', 'inboxbear.com', '10minutemail.com', 'temp-mail.com', 'burnermail.io',
		'tempmailaddress.com', '20minutemail.com', 'maildrop.cc', 'trashmail.com', 'mailimate.com'
	];

	// Sepára los elementos del correo electrónico
	$emailParts = explode('@', $email);
	
	// Contar las partes del correo electrónico
	if (count($emailParts) !== 2) {
		return false;
	}

	// normalizar el dominio del correo
	$domain = strtolower($emailParts[1]);

	// buscar en el array de dominios no autorizados
	if (!in_array($domain, $temporaryDomains)) {
		return false; 
	}

	return true; // se encontró dentro de la lista
}

/**
 * Ayuda a definir anclas o anchors para navegación
 *
 * @param string $anchor
 * @return string
 */
function new_anchor(string $anchor)
{
	return sprintf('%s#%s', CUR_PAGE, $anchor);
}

/**
 * Remueve caracteres con acentos en un string y los sustituye por su
 * versión sin acento del caracter
 *
 * @param string $string
 * @return string
 */
function remove_accents(string $string) {
	$accents     = array('á', 'é', 'í', 'ó', 'ú', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'ñ', 'Ñ');
	$substitutes = array('a', 'e', 'i', 'o', 'u', 'u', 'A', 'E', 'I', 'O', 'U', 'U', 'n', 'N');
	
	return strtr($string, array_combine($accents, $substitutes));
}

/**
 * Retorna la URL de la página actual completa
 *
 * @return string
 */
function get_cur_page()
{
	return CUR_PAGE;	
}