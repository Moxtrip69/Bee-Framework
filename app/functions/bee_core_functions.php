<?php

/**
 * Convierte el elemento en un objecto
 *
 * @param [type] $array
 * @return void
 */
function to_object($array) {
  return json_decode(json_encode($array));
}

/**
 * Regresa el nombre de nuestra aplicación
 *
 * @return void
 */
function get_sitename() {
  return 'Bee framework';
}

/**
 * Regresa la fecha de estos momentos
 *
 * @return void
 */
function now() {
  return date('Y-m-d H:i:s');
}

/**
 * Hace output en el body como json
 *
 * @param array $json
 * @param boolean $die
 * @return void
 */
function json_output($json, $die = true) {
  header('Access-Control-Allow-Origin: *');
  header('Content-type: application/json;charset=utf-8');

  if(is_array($json)){
    $json = json_encode($json);
  }

  echo $json;
  if($die) {
    die;
  }
  
  return true;
}

/**
 * Construye un nuevo string json
 *
 * @param integer $status
 * @param array $data
 * @param string $msg
 * @return void
 */
function json_build($status = 200 , $data = null , $msg = '', $error_code = null) {
  /*
  1 xx : Informational
  2 xx : Success
  3 xx : Redirection
  4 xx : Client Error
  5 xx : Server Error
  */

  if(empty($msg) || $msg == '') {
    switch ($status) {
      case 200:
        $msg = 'OK';
        break;
      case 201:
        $msg = 'Created';
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
    'status' => $status,
    'error'  => false,
    'msg'    => $msg,
    'data'   => $data
  ];

  if (in_array($status, [400,403,404,405,500])){
    $json['error'] = true;
  }

  if ($error_code !== null) {
    $json['error'] = $error_code;
  }

  return json_encode($json);
}

/**
 * Regresa parseado un modulo
 *
 * @param string $view
 * @param array $data
 * @return void
 */
function get_module($view, $data = []) {
  $file_to_include = MODULES.$view.'Module.php';
	$output = '';
	
	// Por si queremos trabajar con objeto
	$d = to_object($data);
	
	if(!is_file($file_to_include)) {
		return false;
	}

	ob_start();
	require_once $file_to_include;
	$output = ob_get_clean();

	return $output;
}

/**
 * Formatea un número a divisa
 *
 * @param float $amount
 * @param string $symbol
 * @return void
 */
function money($amount, $symbol = '$') {
  return $symbol.number_format($amount, 2, '.', ',');
}

/**
 * Carga una opción de configuración de la db
 *
 * @param mixed $option
 * @return void
 */
function get_option($option) {
  return optionModel::search($option);
}

/**
 * Generar un link dinámico con parametros get y token
 * 
 */
function buildURL($url , $params = [] , $redirection = true, $csrf = true) {
	
	// Check if theres a ?
	$query     = parse_url($url, PHP_URL_QUERY);
	$_params[] = 'hook='.strtolower(SITE_NAME);
	$_params[] = 'action=doing-task';

	// Si requiere token csrf
	if ($csrf) {
		$_params[] = '_t='.CSRF_TOKEN;
	}
	
	// Si requiere redirección
	if($redirection){
		$_params[] = 'redirect_to='.urlencode(CUR_PAGE);
	}

	// Si no es un array regresa la url original
	if (!is_array($params)) {
		return $url;
	}

	// Listando parametros
	foreach ($params as $key => $value) {
		$_params[] = sprintf('%s=%s', urlencode($key), urlencode($value));
	}
	
	$url .= strpos($url, '?') ? '&' : '?';
	$url .= implode('&', $_params);
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
function logger($message , $type = 'debug' , $output = false) {
  $types = ['debug','import','info','success','warning','error'];

  if(!in_array($type , $types)){
    $type = 'debug';
  }

  $now_time = date("d-m-Y H:i:s");

  $message = "[".strtoupper($type)."] $now_time - $message";

  if(!$fh = fopen(LOGS."bee_log.log", 'a')) { 
    error_log(sprintf('Can not open this file on %s', LOGS.'bee_log.log'));
    return false;
  }

  fwrite($fh, "$message\n");
	fclose($fh);
	if($output){
		print "$message\n";
	}

  return true;
}

/**
 * Códificar a json de forma especial para prevenir errores en UTF8
 *
 * @param mixed $var
 * @return string
 */
function json_encode_utf8($var) {
  return json_encode($var, JSON_UNESCAPED_UNICODE);
}