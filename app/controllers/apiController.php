<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de api
 */
class apiController extends Controller {


  /**
   * Instancia de la clase BeeHttp
   *
   * @var BeeHttp
   */
  private $http = null;

  /**
   * La información de la petición completa
   * incluyendo el cuerpo de la petición, cabeceras,
   * files y el body RAW
   * 
   * @var array
   */
  private $req  = null;

  /**
   * Información ya formateada conteniendo el cuerpo
   * de la petición actual
   *
   * @var array
   */
  private $data = [];

  function __construct()
  {
    // Prevenir el acceso no autorizado
    if (!defined('DOING_AJAX') && !defined('DOING_API')) die();

    // Procesamos la petición que está siendo mandanda al servidor
    try {
      $this->http = new BeeHttp(__CLASS__);
      $this->req  = $this->http->get_request();
      $this->data = $this->req['data'];
    } catch (BeeHttpException $e) {
      json_output(json_build($e->getStatusCode(), null, $e->getMessage()));
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }
  
  function index()
  {
    http_response_code(404);
    json_output(json_build(404, null, 'Ruta no encontrada.'));
  }

  function posts()
  {
    try {
      $this->http->accept(['get','post','put','delete']);
      $posts = Model::list('pruebas');
      json_output(json_build(200, $posts));
    } catch (BeeHttpException $e) {
      json_output(json_build($e->getStatusCode(), null, $e->getMessage()));
    } catch (Exception $e) {
      json_output(json_build($e->getCode(), null, $e->getMessage()));
    }
  }
}
