<?php 

/**
 * Handler para excepciones de la clase BeeHttp
 * 
 * @since 1.1.4
 * 
 */
class BeeHttpException extends Exception{

  private $statusCode = null;

  public function __construct(String $message, Int $statusCode = 400, Int $code = 0, Exception $previous = null) {
    $this->statusCode = (int) $statusCode;

    parent::__construct($message, $code, $previous);
  }

  public function getStatusCode(){
    return $this->statusCode;
  }

}