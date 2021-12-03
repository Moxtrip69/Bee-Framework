<?php 

class BeeSession {

  private $id; // identificador del usuario en curso
	private $token;

  private $bee_users_table     = BEE_USERS_TABLE;
  private $bee_cookies         = BEE_COOKIES;
	private $bee_cookie_token    = BEE_COOKIE_TOKEN;
	private $bee_cookie_id       = BEE_COOKIE_ID;
	private $bee_cookie_lifetime = BEE_COOKIE_LIFETIME;
  private $bee_cookie_domain   = BEE_COOKIE_DOMAIN;
	private $current_user        = null;


	function __construct()
	{
    // El tipo de sistema a utilizar para trabajar las sesiones de usuarios
    // jstodo: si no están activas las cookies, se trabaja un login o sesiones no persistentes con solamente
    // variables de sesión regualres.
    if ($this->bee_cookies === false) {
      
    } 

    try {
      // Verificar que haya una conexión con la base de datos
      $tables = Model::list_tables();
      if (empty($tables)) {
        throw new Exception('No hay tablas en la base de datos.');
      }
      
      // Verificar que exista la tabla de usuarios en la base de datos
      if (!Model::table_exists($this->bee_users_table)) {
        throw new Exception(sprintf('No existe la tabla %s en la base de datos.', $this->bee_users_table));
      }

      // Proceder solo si todo está en orden
      return true;
      
    } catch (Exception $e) {
      die($e->getMessage());
    }
	}

	public function get_cookie_name($cookie)
	{
		switch ($cookie) {
			case 'token':
				return $this->bee_cookie_token;
				break;
			case 'id':
				return $this->bee_cookie_id;
				break;
			default:
				throw new Exception('No está definido el tipo de cookie solicitado.');
		}
	}

	/**
	* Valida si existe una sesión almacenada en cookies / y si es válida
	* @access public
	* @var $_COOKIE ID_USR $_COOKIE TKN
	* @return array | false
	**/
	public static function authenticate()
	{
		/** We make an instance to use class properties */
		$auth = new self();

		## If the cookies exists
		if (!self::cookie_exists($auth->get_cookie_name('id')) || !self::cookie_exists($auth->get_cookie_name('token'))) {
			self::destroy_cookie([ $auth->get_cookie_name('id'), $auth->get_cookie_name('token') ]);
			return false;
		}

		## Obtenemos la información del usuario
		$usuario = new usuariosModel();
		//$res = $usuario->validar_usuario_y_token(self::get_cookie($auth->get_cookie_name('id')) , hash($auth->hash, self::get_cookie($auth->get_cookie_name('token'))));
		$res = usuariosModel::validate_user_session(self::get_cookie($auth->get_cookie_name('id')) , hash($auth->hash, self::get_cookie($auth->get_cookie_name('token'))));

		## Verificamos si coincide
		if (!$res) {
			## Si no coinciden, destruimos cookies
			self::destroy_cookie([
				$auth->get_cookie_name('id') => NULL, 
				$auth->get_cookie_name('token') => NULL
			]);

			## Regresamos false para redirección
			return false;
		}

		if (!$user = $usuario->cargar_informacion_usuario(self::get_cookie($auth->get_cookie_name('id')) , hash($auth->hash, self::get_cookie($auth->get_cookie_name('token'))))) {
			return false;
		}
		
		return true; // return $user si se presentan errores
	}

	/**
	* Starts the session of the user
	* @access public
	* @var array
	* @return bool
	**/
	public static function new_session($id) 
	{
		// Nueva instancia para usar las propiedades de la clase
		$auth = new self();

		## Creates a new token
		$token   = self::generar_token();
		$usuario = new usuariosModel();

		## Verificamos si existen los cookies
		if (self::cookie_exists($auth->get_cookie_name('id')) && self::cookie_exists($auth->get_cookie_name('token'))) {

			## Si existen los borramos y creamos nuevos y agregamos a DB
			self::destroy_cookie([
				$auth->get_cookie_name('id') => NULL, 
				$auth->get_cookie_name('token') => NULL
			]);
			
			self::new_cookie([
				$auth->get_cookie_name('id') => $id , 
				$auth->get_cookie_name('token') => $token
			]);

			## Update table with new token
			//$usuario->actualizar_token($id , hash($auth->hash, $token));
			$id = usuariosModel::add_user_session($id , hash($auth->hash, $token));

		} else {

			## Create cookies if they dont exist
			self::new_cookie([
				$auth->get_cookie_name('id') => $id , 
				$auth->get_cookie_name('token') => $token
			]);

			## Update table with new token
			//$usuario->actualizar_token($id , hash($auth->hash, $token));
			$id = usuariosModel::add_user_session($id , hash($auth->hash, $token));

		}

		## All good, return true
		return ($id) ? true : false;
	}

	 /**
    * Verifica si existe un cookie en sistema
    *
    * @param string $cookie
    * @return bool
    */
	private static function cookie_exists($cookie)
	{
    return isset($_COOKIE[$cookie]);
	}

	/**
   * Creamos un cookie directamente
   * con base al array de elementos pasados
   * nombre del cookie => valor del cookie
   *
   * @param array $cookies
   * @return void
   */
	private static function new_cookie($cookies)
	{
		$auth = new self();

		foreach ($cookies as $key => $value) {
			setcookie($key , $value , time() + $auth->bee_cookie_lifetime , $auth->bee_cookie_domain);
		}

		return true;
	}

	/**
   * Borrar cookies en caso de existir,
   * se pasa el nombre de cada cookie como parámetro array
   *
   * @param array $cookies
   * @return bool
   */
	private static function destroy_cookie($cookies)
	{
    $auth = new self();

		foreach ($cookies as $key => $value) {
			if (isset($_COOKIE[$key])) {
				setcookie($key , null , time() - 1000 , $auth->bee_cookie_domain);
				return true;
			}
		}

		return false;
	}

	/**
   * Utilizada para destruir una sesión persistente de nuestro usuario
   * loggeado en el sistema
   *
   * @return bool
   */
	public static function destroy_session()
	{
		$auth = new self();

		// Se destruyen todos los tokens generados para que sea imposible el ingreso con ese mismo token después
    $sql = sprintf('DELETE u FROM %s u WHERE id = :id AND auth_token = :token', $auth->bee_users_table);
    if (!Model::query($sql, ['id' => self::get_cookie($auth->get_cookie_name('id')), 'auth_token' => hash($auth->hash, self::get_cookie($auth->get_cookie_name('token')))])) {
      return false;
    }
		
    // Se destruyen todos los cookies existentes
		self::destroy_cookie(
      [
			  $auth->get_cookie_name('id')    => null, 
			  $auth->get_cookie_name('token') => null
      ]
    );
			
		// Se destruyen todas las posibles variables de sesión para limpiarlas del disco duro
		unset($_SESSION);
		session_destroy();
		
		return true;
	}

  /**
   * Verifica si existe un determinado cookie creado
   *
   * @param string $cookie_name
   * @return mixed
   */
	public static function get_cookie($cookie)
	{
		return isset($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : false;
	}
}