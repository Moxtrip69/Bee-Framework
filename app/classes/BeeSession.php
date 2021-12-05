<?php 

class BeeSession {

  private $id; // identificador del usuario en curso
	private $token;

  private $bee_users_table     = BEE_USERS_TABLE;
  private $bee_cookies         = BEE_COOKIES;
	private $bee_cookie_id       = BEE_COOKIE_ID;
	private $bee_cookie_token    = BEE_COOKIE_TOKEN;
	private $bee_cookie_lifetime = BEE_COOKIE_LIFETIME;
  private $bee_cookie_path     = BEE_COOKIE_PATH;
	private $bee_cookie_domain   = BEE_COOKIE_DOMAIN;
	private $current_user        = null;


	function __construct()
	{
		// Validar que todo esté en orden configurado
		$this->check_if_ready();
	}
	
	/**
	 * Verificamos que las configuraciones
	 * sean correctas para poder trabajar
	 * con sesiones persistentes
	 *
	 * @return bool
	 */
	public function check_if_ready()
	{
		// Se verifica la existencia correcta de las constantes requeridas y variables
		try {
			if ($this->bee_cookies === false || !defined('BEE_COOKIES')) {
				bee_die(sprintf('Es requerida la constante %s para poder trabajar con sesiones persistentes de %s', 'BEE_COOKIES', get_bee_name()));
			} 

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
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Regresa el nombre de alguno de nuestros 2 cookies utilizados en autenticación
	 *
	 * @param string $cookie
	 * @return bool
	 */
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
		// Instancia de nuestra BeeSession
		$auth = new self();

		// Validar la existencia de los cookies en el sistema
		if (!self::cookie_exists($auth->bee_cookie_id) || !self::cookie_exists($auth->bee_cookie_token)) {
			// Si no existe la coincidencia vamos a borrar los cookies por seguridad
			self::destroy_cookie([
				$auth->bee_cookie_id    => null, 
				$auth->bee_cookie_token => null
			]);

			return false;
		}

		// Verificamos que exista el usuario con base a la información de nuestro cookie
		if (!$auth->current_user = Model::list($auth->bee_users_table, ['id' => self::get_cookie($auth->bee_cookie_id)], 1)) {
			return false;
		}

		// Información del usuario
		$user       = $auth->current_user;
		$auth_token = $user['auth_token'];
		$token      = self::get_cookie($auth->bee_cookie_token);

		// Verificamos si coincide la información
		if (!password_verify($token, $auth_token)) {
			// Si no existe la coincidencia vamos a borrar los cookies por seguridad
			self::destroy_cookie([
				$auth->bee_cookie_id    => null, 
				$auth->bee_cookie_token => null
			]);

			return false;
		}
		
		return $user; // return $user si todo es correcto
	}

	/**
	* Inicia la sesión persistente del usuario en curso
	* @access public
	* @var array
	* @return bool
	**/
	public static function new_session($id) 
	{
		// Nueva instancia para usar las propiedades de la clase
		$auth  = new self();

		// Creamos un nuevo token
		$token = generate_token();

		// Cargamos la información del usuario
		$user  = Model::list($auth->bee_users_table, ['id' => $id], 1);

		if (empty($user)) {
			return false; // no existe el usuario en curso
		}

		// Verificamos si existen los cookies para borrarlos y generar nuevos
		if (self::cookie_exists($auth->bee_cookie_id) || self::cookie_exists($auth->bee_cookie_token)) {
			// Si existen los borramos
			self::destroy_cookie([
				$auth->bee_cookie_id    => null, 
				$auth->bee_cookie_token => null
			]);
		}

		// Creamos nuevos cookies
		self::new_cookie([
			$auth->bee_cookie_id    => $id, 
			$auth->bee_cookie_token => $token
		]);

		// Actualizamos el token en la base de datos
		Model::update($auth->bee_users_table, ['id' => $id], ['auth_token' => password_hash($token, PASSWORD_BCRYPT)]);

		return true;
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

		foreach ($cookies as $cookie_name => $cookie_value) {
			setcookie($cookie_name , $cookie_value , time() + $auth->bee_cookie_lifetime , $auth->bee_cookie_path, $auth->bee_cookie_domain);
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

		foreach ($cookies as $cookie_name => $cookie_value) {
			if (isset($_COOKIE[$cookie_name])) {
				setcookie($cookie_name , null , time() - 1000 , $auth->bee_cookie_path, $auth->bee_cookie_domain);
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
    if (!Model::update($auth->bee_users_table, ['id' => self::get_cookie($auth->bee_cookie_id)], ['auth_token' => ''])) {
      return false;
    }
		
    // Se destruyen todos los cookies existentes
		self::destroy_cookie(
      [
			  $auth->bee_cookie_id    => null, 
			  $auth->bee_cookie_token => null
      ]
    );
	
		// Se regresa true si se borra todo con éxito
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