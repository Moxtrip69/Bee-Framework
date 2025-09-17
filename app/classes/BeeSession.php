<?php 

class BeeSession {

	/**
	 * Identidicador del usuario en curso
	 *
	 * @var mixed
	 */
  private $id;

	/**
	 * Token de acceso del usuario
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Tabla dónde se almacena la información de usuarios
	 *
	 * @var string
	 */
  private $bee_users_table     = BEE_USERS_TABLE;

	/**
	 * Determina si se usarán o no cookies
	 * @deprecated 1.6.0
	 * @var bool
	 */
  private $bee_cookies         = null;

	/**
	 * Nombre del cookie para el ID
	 *
	 * @var string
	 */
	private $bee_cookie_id       = BEE_COOKIE_ID;

	/**
	 * Nombre del cookie para el token
	 *
	 * @var string
	 */
	private $bee_cookie_token    = BEE_COOKIE_TOKEN;

	/**
	 * Tiempo de expiración del cookie en segundos
	 *
	 * @var int
	 */
	private $bee_cookie_lifetime = BEE_COOKIE_LIFETIME;
  private $bee_cookie_path     = BEE_COOKIE_PATH;
	private $bee_cookie_domain   = BEE_COOKIE_DOMAIN;
	
	/**
	 * Información del usuario
	 *
	 * @var ?array
	 */
	private $current_user        = null;

	/**
	 * Algoritmo de hasheo a utilizar para el token de sesión
	 *
	 * @var string
	 */
	private $hash                = "SHA256";


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
	* Valida si existe una sesión almacenada en cookies / y si es válida
	* @access public
	* @var $_COOKIE ID_USR $_COOKIE TKN
	* @return array | false
	**/
	public static function authenticate()
	{
		// Instancia de nuestra BeeSession
		$self = new self();

		// Validar la existencia de los cookies en el sistema
		if (!cookie_exists($self->bee_cookie_id) || !cookie_exists($self->bee_cookie_token)) {
			// Si no existe la coincidencia vamos a borrar los cookies por seguridad
			destroy_cookie($self->bee_cookie_id, $self->bee_cookie_path, $self->bee_cookie_domain);
			destroy_cookie($self->bee_cookie_token, $self->bee_cookie_path, $self->bee_cookie_domain);

			return false;
		}

		// Cargamos datos en cookies
		$self->id = get_cookie($self->bee_cookie_id);

		// Verificamos que exista el usuario con base a la información de nuestro cookie
		if (!$self->current_user = Model::list($self->bee_users_table, ['id' => $self->id], 1)) {
			return false;
		}

		// Información del usuario
		$self->token = get_cookie($self->bee_cookie_token); // el token guardado en la cookie

		// Verificamos si coincide la información
		if (!$self->validateUserSession($self->id, $self->token)) {
			// Si no existe la coincidencia vamos a borrar los cookies por seguridad
			destroy_cookie($self->bee_cookie_id, $self->bee_cookie_path, $self->bee_cookie_domain);
			destroy_cookie($self->bee_cookie_token, $self->bee_cookie_path, $self->bee_cookie_domain);

			return false;
		}
		
		return $self->current_user; // regresa data del usuario si es correcta la sesión
	}

	/**
	* Inicia la sesión persistente del usuario en curso
	* @access public
	* @var array
	* @return bool
	**/
	public static function new_session(?array $usuario) 
	{
		// Nueva instancia para usar las propiedades de la clase
		$self                = new self();

		// Validamos la información del usuario
		$self->current_user  = $usuario;

		if (empty($self->current_user)) {
			return false; // No hay información del usuario
		}

		// Creamos un nuevo token
		$self->token         = generate_token();

		// Verificamos si existen los cookies para borrarlos y generar nuevos
		if (cookie_exists($self->bee_cookie_id) || cookie_exists($self->bee_cookie_token)) {
			// Si existen los borramos
			destroy_cookie($self->bee_cookie_id, $self->bee_cookie_path, $self->bee_cookie_domain);
			destroy_cookie($self->bee_cookie_token, $self->bee_cookie_path, $self->bee_cookie_domain);
		}

		// Registramos la nueva sesión del usuario
		$self->addUserSession($self->current_user['id'], $self->token);

		// Creamos nuevos cookies
		new_cookie($self->bee_cookie_id, $self->current_user['id'], $self->bee_cookie_lifetime, $self->bee_cookie_path, $self->bee_cookie_domain);
		new_cookie($self->bee_cookie_token, $self->token, $self->bee_cookie_lifetime, $self->bee_cookie_path, $self->bee_cookie_domain);

		return true;
	}

	/**
   * Utilizada para destruir una sesión persistente de nuestro usuario
   * loggeado en el sistema
   *
   * @return bool
   */
	public static function destroy_session()
	{
		$self = new self();

		// Se destruye el token que coincida en cookies y en la base de datos
    $self->id    = get_cookie($self->bee_cookie_id);
		$self->token = get_cookie($self->bee_cookie_token);

		// Borrar de la base de datos
		$self->destroyUserSession($self->id, $self->token);
		
    // Se destruyen todos los cookies existentes
		destroy_cookie($self->bee_cookie_id, $self->bee_cookie_path, $self->bee_cookie_domain);
		destroy_cookie($self->bee_cookie_token, $self->bee_cookie_path, $self->bee_cookie_domain);
	
		// Se regresa true si se borra todo con éxito
		return true;
	}

	/**
	 * Registra en la base de datos la sesión y el dispositivo con su respectivo token
	 *
	 * @param integer|null $self->id
	 * @param string|null $token
	 * @return mixed
	 */
	function addUserSession(?int $id_usuario, ?string $token)
	{
		$self        = new self();
		$self->id    = $id_usuario;
		$self->token = $token;
		$new_session =
		[
			'id_usuario'        => $self->id,
			'token'             => hash($self->hash, $self->token),
			'navegador'         => get_user_browser(),
			'sistema_operativo' => get_user_os(),
			'ip'                => get_user_ip(),
			'validez'           => strtotime('+1 month'),
			'creado'            => now()
		];

		return Model::add('bee_sessions' , $new_session) ? true : false;
	}

	/**
	 * Verifica si una sesión y dispositivo es correcto para un usuario basado en el token
	 *
	 * @param integer|null $self->id
	 * @param string|null $token
	 * @return bool
	 */
	function validateUserSession(?int $id_usuario, ?string $token)
	{
		$self        = new self();
		$self->id    = $id_usuario;
		$self->token = $token;
		$sql         = 
		'SELECT 
		u.*,
		st.token,
		st.validez
		FROM %s u
		JOIN %s st ON st.id_usuario = u.id AND st.token = :token AND st.validez > :now
		WHERE u.id = :id_usuario
		LIMIT 1';
		$sql = sprintf($sql, BEE_USERS_TABLE, 'bee_sessions');

		return Model::query($sql, ['id_usuario' => $self->id , 'token' => hash($self->hash, $self->token) , 'now' => time()]) ? true : false;
	}

	/**
	 * Destruye una determinada sesión de la base de datos basado en el token
	 *
	 * @param integer|null $self->id
	 * @param string|null $token
	 * @return bool
	 */
	function destroyUserSession(?int $id_usuario, ?string $token)
	{
		$self        = new self();
		$self->id    = $id_usuario;
		$self->token = $token;
		$sql         = 'DELETE st FROM %s st WHERE st.id_usuario = :id_usuario AND st.token = :token';
		$sql         = sprintf($sql, 'bee_sessions');

		return Model::query($sql,['id_usuario' => $self->id , 'token' => hash($self->hash, $self->token)]) ? true : false;
	}
}