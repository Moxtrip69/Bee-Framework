[![Únete al servidor de Discord](https://badgen.net/discord/members/wTzhKrg)](https://discord.gg/wTzhKrg)
[![Únete al grupo de WhatsApp](https://badgen.net//static/Únete%20al%20grupo/WhatsApp/25D366)](https://chat.whatsapp.com/GX86T4pVIFvCdMyovY5UgP)
![Bee Framework](https://badgen.net/static/stars/★★★★★)
![Bee Framework](https://badgen.net/github/watchers/moxtrip69/bee-framework)
![Bee Framework](https://badgen.net/github/forks/moxtrip69/bee-framework)
[![Estudiantes](https://badgen.net//static/Estudiantes/+95,000/f2a)](https://www.academy.joystick.com.mx/)

<img src="https://raw.githubusercontent.com/Moxtrip69/Bee-Framework/master/assets/images/bee_logo.png" alt="Bee Framework" style="width: 250px;">

# Bee Framework
Mini framework desarrollado por la Academia de Joystick.
Puedes hacer uso de el para tus proyectos personales o comerciales, es ligero y fácil de implementar para proyectos tanto pequeños como aquellos que requieren escalabilidad y visión a futuro.

## Changelog
### v 1.5.8
- Revisa el curso oficial sobre esta versión de **Bee framework 1.5.8** dando clic [aquí](https://www.academy.joystick.com.mx/courses/novedades-bee-framework-1-5-8-mejoras-y-actualizaciones).
- Nueva clase **BeeRoleManager** para gestión de roles y permisos, un sistema muy flexible y escalable para gestionar el acceso de usuarios con diferentes roles y permisos asignados por role. Es necesario *actualizar la base de datos* con un nuevo esquema que incluye *3 tablas nuevas*: **bee_roles, bee_permisos y bee_roles_permisos**, requiere usar el archivo *db_beeframework.sql*.
```php
/**
 * El uso es muy sencillo, todo se basa en el slug del role y los permisos asignados al role.
 * Con el handler puedes hacer todas las tareas necesarias:
 * Desde agregar roles, actualizar, borrar, asignar permisos, crear permisos, borrar permisos.
 */

// Validar un permiso
$userRole = 'vendedor';
$role     = new BeeRoleManager($userRole);

if ($role->can('agregar-ventas')) {
  echo '¡Bienvenido, puedes agregar ventas!';
} else {
  echo 'No puedes agregar más ventas.';
}

// Crear un role nuevo
$role = new BeeRoleManager();
$role->addRole('Diseñador Gráfico', 'dg');

// Editar un role
$role->updateRole(3, 'Diseñador Gráfico Editado', 'dge');

// Borrar un role
$role->removeRole('dge');

// Crear permisos
$permiso = new BeeRoleManager();
$permiso->addPermission('Crear imágenes', 'crear-imagenes', 'Puede crear imágenes y descargarlas.');

// Asignar permisos a un role
$role = new BeeRoleManager('dg');
$role->allow('crear-imagenes');

// O quitar permisos a un role
$role = new BeeRoleManager('dg');
$role->deny('crear-imagenes');
```
- El controlador principal **Controller.php** se ha mejorado y hemos ampliado la forma en que se usa, ahora es posible usarlo para configurar cada nuevo controlador de diferentes maneras, puede ser un *endpoint* para talvez una API, *ajax* o *regular* como un controlador común, ahora tenemos a nuestra disposición nuevos métodos para trabajar de forma orientada a objetos la implementación, el renderizado de la vista, asignación de **$data** pasada a la vista y mucho más, haciendo todo más mantenible y escalable.
```php
class productosController extends Controller implements ControllerInterface {

  function __construct()
  {
    parent::__construct('endpoint'); // Define que es un endpoint
  }
}
```
- Nueva clase **BeeHooksManager** para crear y administrar ganchos o hooks a lo largo del flujo de ejecución del framework, aumentando las posibilidades y configuraciones de manera exponencial sin la necesidad de modificar archivos de configuración o del core.
```php
// classes/Bee.php

class Bee {
  // ..............
  private function init()
  {
    // Todos los métodos que queremos ejecutar consecutivamente
    $this->init_session();
    $this->init_load_config();
    $this->init_framework_properties();
    $this->init_load_composer(); // Carga las dependencias de composer
    $this->init_autoload(); // Inicializa el cargador de nuestras clases
    $this->init_load_functions();
    BeeHookManager::runHook('init_set_up', $this);
    BeeHookManager::runHook('after_functions_loaded'); // Gancho o Hook definido
  }
  // ..............
}
```

```php
// bee_custom_functions.php

/**
 * Carga el archivo de funciones para las clases en vivo, tutoriales y streams de Joystick
 * Puedes borrar todo esto sin problema alguno o usarlo cómo referencia para tus proyectos
 *
 * @return void
 */
function load_joystick_functions()
{
  require_once FUNCTIONS . 'puedes_borrarlas.php';
}

/**
 * Se ejecuta el hook después de la carga de todas las funciones del core
 */
BeeHookManager::registerHook('after_functions_loaded', 'load_joystick_functions');
```
- Se han hecho mejoras al archivo de **settings.php** y al flujo de carga y ejecución del framework, ahora con el sistema de hooks es posible crear nuevas constantes y configuraciones sin necesidad de tocar los archivos **settings.php** y **bee_config.php**.
- Nuevas mejoras en la interfaz y en la pantalla de bienvenida del framework, con base a su feedback se re-acomodaron los elementos de navegación.
- Nueva clase **BeeCartHandler** y sub-clases que permiten la creación de carritos de compra persistentes para la sesión del usuario, totalmente funcional y dinámico, puede ser usado desde el inicio para procesos de tiendas en línea, junto con esto viene un nuevo controlador llamado **tiendaController** dónde se listan todos los productos de la base de datos (nueva tabla *products*) en el esquema inicial, y nuevo controlador **carritoController** para mostrar el carrito y proceso de *Checkout* de forma profesional, puede ser editado y alterado a necesidad.
- Nueva clase **BeeFormBuilder** para construir de forma dinámica formularios, cómo se lista en la versión anterior.
- Ahora es posible generar controladores con **Creator** y seleccionar si la vista a crear será utilizando el motor nativo de *Bee* o si se deberá utilizar *Twig*, el nuevo motor implementado en Bee framework.
- Nuevo panel de administración implementado y listo para ser utilizado en un controlador seguro llamado **adminController**, ahora el perfil de usuario será mostrado ahí, y puede ser utilizado para gestionar o administrar el sistema que se esté construyendo, se ha utilizado la plantilla *SB Admin 2* con Bootstrap, puedes hacer uso de ese mismo o actualizar al de tu preferencia.
### v 1.5.5
- Se ha anexado una nueva clase para construir de forma dinámica, rápida y mantenible formularios, la clase la encuentras como **BeeFormHandler**, puede ser utilizada desde este momento para cualquier proyecto, estamos trabajando en extender sus funcionalidades para hacerlo aún más flexible. Actualmente soporta los tipos de campos más utilizados como *text, email, password, select, checkbox, radio, range, number, phone, url, date y file*. De igualmente manera incluye una funcionalidad interesante para generar un script para enviar la información de los campos del formulario usando AJAX de forma automática.
- La API es 100% funcional y segura de ser utilizada en proyectos en producción, todo está funcionando de forma estable y segura, puedes extender la funcionalidad a todos los controladores, pero deberás añadirlos a la lista de controladores que serán interpretados como endpoints generales de la API.
- Ahora la función **logger()** registra la información en dos archivos diferentes, *bee_log.log* para producción y *dev_log.log* para desarrollo, así no hay mezcla de información en el registro de los eventos.
- Se ha implementado un sistema para configuraciones basadas en registros dentro de la base de datos utilizando el modelo **optionModel**, podrás guardar valores de configuración y cargarlos para añadir seguridad o personalización a cualquier sistema de inmediato, utiliza el método **optionModel::search(opción)** o la función **get_option(nombre de la opción)** para buscar la opción en la base de datos y obtener su valor, para crear una nueva opción o actualizarla usa **optionModel::save(opción, valor)**, si no existe será creada, si existe será actualizada.
- Open Graph añadido para mejorar el SEO de forma rápida y sencilla de cualquier artículo o sección del sistema.
- Estamos implementado **Twig** como motor de plantillas para simplificar la lógica de presentación y mejorar la reutilización de código en nuestras vistas. Esto permitirá una separación más clara entre la lógica de negocio y la presentación, lo que a su vez mejorará la mantenibilidad de nuestro código. Twig ofrece una sintaxis clara y fácil de entender, lo que agilizará el proceso de desarrollo y reducirá la posibilidad de errores en nuestras plantillas. Gracias a su sistema de almacenamiento en caché, Twig mejorará el rendimiento general de nuestras vistas, asegurando una experiencia más fluida para nuestros usuarios, es importante entender que aún está en desarrollo la implementación de la forma más flexible posible.
- Se están creando dos nuevas tablas en la base de datos: **posts** y **posts_meta**. Estas tablas están diseñadas para almacenar información genérica de cualquier tipo, lo que las hace muy versátiles y adecuadas para diversos proyectos. En la tabla **posts**, se pueden almacenar diferentes tipos de registros, como visitas, comentarios, vistas, entradas de blog, noticias, productos, servicios, mensajes y cualquier otro tipo de dato relevante. La idea detrás de esta tabla es que sea lo suficientemente flexible para adaptarse a cualquier requerimiento sin necesidad de crear tablas específicas para cada tipo de dato. Por otro lado, se tiene la tabla **posts_meta**, que se utilizará para almacenar metadatos relacionados con los registros guardados en la tabla **posts**. Los metadatos son datos adicionales que proporcionan información sobre los registros, como etiquetas, categorías, fechas, autor, o cualquier otro atributo relevante que pueda variar según el tipo de contenido almacenado.
- Se han corregido errores muy importantes dentro de funciones core del sistema, sobre todo en el generador de enlaces dinámicos, ya que tenia problemas si se repetían parámetros y se generaban de forma incorrecta.
- Ahora es posible escoger dentro de 5 temas de **Bootstrap 5**, solo deberás actualizar la constante **CSS_FRAMEWORK** a alguno de los siguientes valores:
  - Zephyr (*bs_zephyr*)
  - Litera (*bs_litera*)
  - Lumen (*bs_lumen*)
  - Vapor (*bs_vapor*)
  - Lux (*bs_lux*)
- Hemos corregido diversos bugs y funciones que ya se encontraban deprecadas para las últimas versiones de **PHP**.
- Nuevo sistema sencillo para generar usuarios de prueba.
- Los controladores y elementos de Bee Framework ya no serán accesibles si el sitio no se encuentra en desarrollo o servidor local.
- Se actualizó la forma en que se carga la información del usuario al estar loggeado, antes se cargaba la información solo al iniciar sesión o ingresar, ahora la información del usuario es actualizada cada vez que se hace una nueva petición, haciendo que está se encuentre siempre actualizada.
- Se re-estructuraron las carpetas para plantillas de **Creator**, ahora se encuentran dentro de *views/modules/bee/*.
- Se realizaron ajustes al controlador de errores, la vista se ha renombrado y por defecto se regresa un **http code 404**, la vista principal ahora se llama **errorView.php**.
- Nuevos métodos estáticos mejorados para generar notificaciones tipo flash, ahora con títulos en cada alerta e iconos.
### v 1.5.0
- Se ha sustituido el controlador por defecto de **homeController** a **beeController** esto para facilitar el actualizado del core a nuevas versiones una vez en producción, facilitando el trabajo de los desarrolladores que lo utilizan.
- Se ha implementado el uso de Vue.js 3 con base a **CDN** y no CLI, puede ser removido retirando la etiqueda en el archivo **styles.php**.
- Nueva clase **BeePdf** implementada para la generación de forma sencilla de documentos formato **pdf** con pocas línea de código, como base se utiliza la librería **dompdf** una de las más potentes y utilizadas actualmente.
- Hemos mejorado el sistema **ORM** para manipulación de bases de datos con nuevos métodos y opciones de configuración.
- Nuevos elementos de configuración agregados al framework para controlar que elementos pueden ser incluidos sin tener que editar el código, para mejorar el tiempo de prototipado o pruebas de concepto, puedes encontrar todas las nuevas variables de configuración en **settings.php**.
- Hemos incluido de forma práctica los *CDN* para los frameworks **CSS** más utilizados en el mercado, **Bootstrap 5, Bulma y Foundation**, puedes configurar cuál incluir desde **settings.php**.
- Se han agregado nuevas funciones que facilitan la personalización de Bee framework al implementarse desde 0.
- Hemos creado una nueva sección especial para mostrar la información actual de Bee framework y todas las configuraciones aplicadas, similar a phpinfo().
- La creación de conexiones a la base de datos ahora es accesible de forma pública para poder conectarse sin necesidad de tener que hacer un query directamente.
- Nuevos archivos **includes** creados para evitar problemas de visualización para rutas específicas del framework como lo es **creator** o **bee**.
- La clase **Flasher** ha recibido una actualización para trabajar con los 3 principales frameworks **CSS** también en sus estilos para las notificaciones flash.
- Nuevo sistema sencillo para generar contraseñas de reemplazo en **bee/password/$password** en caso de querer actualizar de forma sencilla la contraseña por defecto que es **123456** y usuario **bee**, se reemplaza en **loginController/post_login**.
- Hemos mejorado la herramienta **Creator** para prevenir el borrado de archivos ya existentes y alertar al usuario.
- Nuevas mejoras a la creación de modelos, ahora es posible determinar el nombre de la tabla directamente desde **Creator** y también un esquema sencillo de la tabla para ahorrarnos tiempo al trabajar.
- Nuevo sistema de sesiones persistentes para mantener con **cookies** la sesión del usuario en curso abierta, funciona actualmente para solo un dispositivo a la vez, es decir si se inicia sesión en otro dispositivo se cerrará en el anterior y se conservará persistente en el dispositivo en curso, para activarlas es necesario editar las nuevas constantes en *settings.php*.
- Nuevas mejoras en el sistema para enviar correos electrónicos, ahora es posible configurar de forma directa credenciales para hacer uso de servidor con SMTP.
- Nuevos métodos agregados a nuestra clase **Flasher** para agilizar el desarrollo con accesos rápidos a **success, danger, warning, info** entre otros, más información en **Flasher.php**.
- Nuevos mensajes por defecto para ser reutilizados de forma estándar en todo el sistema, usando la función *get_bee_message($codigo);* y se pueden registrar nuevos mensajes para estar disponibles en todo el sistema con *register_bee_custom_message($codigo, $mensaje);*.
- Nuevos métodos rápidos para el módelo principal, entre ellos:
  - class::drop($table);
  - class::truncate($table);
  - class::create($table_name, $schema, $drop = false);
- Nuevos estilos para plugin Toastr y configuraciones adicionales por defecto, todo esto puede ser sobre-escrito en **main.js**
- Se han deprecado los parámetros *hook* y *action* en peticiones **AJAX**, solo es requerido el verbo de la petición, y si el tipo de petición así lo requiere, el token **CSRF** para evitar ataques cross-domain.
- Se han anexados parámetros de configuración globales para el *objeto de Javascript* disponible en el pie de página del sitio, puede ser accedido desde cuanlquier ruta del sistema, ahora es posible registrar nuevos elementos globales o locales.


### v 1.1.3
- Ahora es posible cargar un favicon para el sistema con una nueva función **get_favicon()**.
- Nuevas mejoras generales en el framework.
- Se ha corregido el bug donde era imposible borrar más de 1 registro usando el método **remove** del modelo general sin específicar una cantidad de registros a borrar, ahora el valor por defecto será **todos los registros coincidientes** y en caso de no requerir todos, se necesitará especificar.
- Se mejoró el sistema de variables del sistema insertadas como objeto **Bee** en el pie del sitio para ser accesibles en **javascript**.
- Ahora es posible registrar nuevos valores en el objeto **Bee** desde cualquier método o ruta del sistema usando la función **register_to_bee_obj()**.
- Se mejoró la seguridad del framework y su integridad ante accesos no autorizados implementando archivos **.htaccess** colocados en las rutas principales que requieren seguridad adicional, esto impedirá que algún usuario pueda listar y visualizar los archivos de forma directa en algún directorio.
- Nuevas funciones core para agilizar el desarrollo utilizando Bee framework.
- Se ha hecho obligatorio el uso de token **CSRF** al realizar peticiones tipo **post | put | delete | add |headers** al controlador **ajax**.
- Sistema de log de eventos que puede ser utilizado para registrar en un archivo **.log** cualquier información que necesitemos.

### v 1.1.1
- Se corrigió el error en la constante **UPLOADS**, se encontraba mal formateada y con diagonales invertidas adicionales no necesarias que producian errores al cargar archivos.
- Se mejoró la compatibilidad con Bootstrap 5 Beta en todo el framework.
- Nuevas funciones core agregadas para facilitar el flujo de desarrollo de cualquier proyecto.
- Se han separado las hojas de estilos incrustadas en el header al archivo **styles.php** y los scripts al archivo **scripts.php** para facilitar la reutilización de los mismos.
- Nuevo sistema para registrar hojas de estilo en la cabecera y archivos de scripts en el pie de página de forma manual.
- Nuevo sistema para registrar un objeto javascript **Bee** que da acceso a los mismos parámetros que tenemos disponibles en **PHP** para las rutas de archivos, csrf, url y más opciones para registrar nuevos valores.

### v 1.1.0
- Hemos cambiado algunos archivos de configuración para facilitar la escalabilidad y soporte al código, separando las constantes de bases de datos y basepath a **bee_config.php** y creando **settings.php**, separando del resto para mejorar su encapsulamiento.
- Nuevas funciones añadidas para carga de información de usuario registrada en el payload de las variables de sesión.
- Mejoras realizadas en el creador de controladores y sus plantillas por defecto, para agilizar el flujo de trabajo.
- Se añadieron nuevos parámetros a la clase **Db.php** para regresar solo regultados como **array asociativo**.
- Actualizamos el sistema para funcionar completamente con **Bootstrap 5 Beta**.
- Seguimos realizando mejoras y corrección de bugs que reporta la comunidad.
- Solucionamos un bug común en la función de enviar email, añadiendo simplemente el método **$mail->isSMTP()**, con esto funcionará sin problema alguno dependiendo la implementación requerida.
- Ahora el creador de controladores también genera de forma inmediata una carpeta con las vistas iniciales y métodos iniciales a utilizar en cualquier proyecto en general.

### Síguenos
Recuerda que tenemos contenido gratuito y excelentes scripts y sistemas listos para usar en nuestra [**Academia de Joystick**](https://www.joystick.com.mx), además de cursos en línea para que exprimas tu potencial al máximo.