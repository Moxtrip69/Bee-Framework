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

## Estado actual — Bee Framework 1.6.0

La rama actual estabiliza el núcleo para que HTTP, CLI, cron, pruebas y el actualizador puedan compartir configuración y servicios sin simular una petición web. El sistema heredado continúa disponible como capa de compatibilidad mientras las aplicaciones migran gradualmente.

### Requisitos

- PHP 8.2 o superior.
- Extensiones PHP: `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `fileinfo`, `hash`, `sodium` y `zip`.
- Composer para desarrollo, actualización del autoload en `app/` y ejecución de las herramientas del proyecto.

### Bootstraps por contexto

Cada punto de entrada carga primero el bootstrap común y después únicamente la capa que necesita:

- `app/bootstrap/common.php`: raíz del proyecto, configuración tipada, contenedor y servicios compartidos.
- `app/bootstrap/http.php`: contexto HTTP, sesión y despacho web.
- `app/bootstrap/cli.php`: comandos sin sesión ni controladores HTTP.
- `app/bootstrap/cron.php`: tareas programadas sin simular `$_SERVER`.
- `app/bootstrap/testing.php`: entorno aislado para pruebas.
- `app/bootstrap/updater.php`: servicios necesarios para inspeccionar actualizaciones.

La raíz del proyecto se resuelve desde `__DIR__`; ya no depende del directorio desde el que se ejecutó PHP.

### Router moderno compatible

Las rutas nuevas se declaran directamente, de forma similar a Laravel, sin envolver el archivo en un `return` ni en una función:

```php
<?php

declare(strict_types=1);

use Bee\Core\Routing\Route;

Route::get('/', [homeController::class, 'index'])->name('home');

Route::get('/usuarios/{id}', [userController::class, 'show'])
    ->whereNumber('id')
    ->name('users.show');

Route::post('/usuarios', [userController::class, 'store'])
    ->middleware('csrf')
    ->name('users.store');

Route::put('/usuarios/{id}', [userController::class, 'update']);
Route::delete('/usuarios/{id}', [userController::class, 'destroy']);
```

Los archivos principales están en `app/routes/web.php`, `app/routes/api.php` y `app/routes/middleware.php`. Se admiten `GET`, `HEAD`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, rutas con parámetros y restricciones, nombres, grupos, middleware y generación de URL mediante `route()`.

El router moderno se evalúa primero. Cuando ninguna ruta nueva coincide, Bee conserva el despacho heredado, por lo que las rutas existentes no necesitan migrarse de inmediato.

### CLI

El núcleo puede cargarse sin ejecutar `Bee::fly()`, iniciar sesión ni despachar controladores:

```bash
php bee core:status
```

### Configuración en aplicaciones

Las variables propias de cada proyecto se agregan a `app/config/.env` y pueden consultarse sin modificar el núcleo:

```php
$apiKey = config('NOTIFICATIONS_API_KEY');
$limit = (int) config('ITEMS_PER_PAGE', '20');
```

Las configuraciones persistidas en la tabla `options` utilizan una API equivalente y se consultan bajo demanda:

```php
$color = option('site.color', '#ffcc00');
```

En controladores del router moderno también se puede inyectar la configuración:

```php
use Bee\Core\Config\Configuration;

final class NotificationController
{
    public function __construct(private readonly Configuration $configuration)
    {
    }
}
```

`get_option()` se mantiene como alias compatible para desarrollos existentes.

### SanitizaciÃ³n de datos

Bee incluye funciones modernas para normalizar entradas comunes. Los sanitizadores tipados retornan `null` cuando el valor no es vÃ¡lido:

```php
$name = sanitize_name($_POST['name'] ?? '');
$phone = sanitize_phone($_POST['phone'] ?? null);
$email = sanitize_email($_POST['email'] ?? null);
$address = sanitize_address($_POST['address'] ?? '');
$age = sanitize_integer($_POST['age'] ?? null, 18, 120);
$quantity = sanitize_number($_POST['quantity'] ?? null, 0);
$amount = sanitize_money($_POST['amount'] ?? null); // "1234.50"
$active = sanitize_boolean($_POST['active'] ?? null);
$website = sanitize_url($_POST['website'] ?? null);
$slug = sanitize_slug($_POST['title'] ?? '');
```

También está disponible `sanitize_string()` para texto general. La sanitización debe combinarse con validación de negocio, consultas preparadas y escape contextual al generar HTML.

### Modelos y consultas ORM

`BeeModel` conserva sus APIs anteriores y añade consultas fluidas, hidratación de modelos, asignación masiva controlada, casts, timestamps y seguimiento de cambios:

```php
final class ArticleModel extends BeeModel
{
    protected string $table = 'articles';
    protected array $fillable = ['title', 'status', 'views'];
    protected array $casts = ['views' => 'integer'];
    protected bool $timestamps = true;
}

$article = ArticleModel::create([
    'title' => 'Bee moderno',
    'status' => 'draft',
    'views' => 0,
]);

$published = ArticleModel::where('status', 'published')
    ->where('views', '>=', 100)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

$article = ArticleModel::find(10);
$article->status = 'published';
$article->save();
```

También están disponibles `whereIn()`, `whereNull()`, `first()`, `value()`, `count()`, `exists()`, `paginate()`, actualizaciones y eliminaciones masivas condicionadas. Estas últimas exigen al menos una cláusula `WHERE` para evitar cambios accidentales sobre toda la tabla.

Los métodos históricos `fetchAll()`, `find(array)`, `first(array)`, `column()` y `query()` permanecen compatibles.

### Ejemplo completo: rutas, controlador y modelo

El repositorio incluye un ejemplo REST funcional compuesto por:

- `app/routes/api.php`: rutas agrupadas y nombradas con distintos verbos HTTP.
- `app/controllers/exampleArticleController.php`: inyección de configuración, request tipado, sanitización, validación y respuestas JSON.
- `app/models/exampleArticleModel.php`: asignación masiva, casts, timestamps y consultas fluidas.
- `database/migrations/20260718_create_example_articles.sql`: esquema reversible de la tabla utilizada.

Las rutas están desactivadas de manera predeterminada. Después de ejecutar la migración, se habilitan localmente agregando lo siguiente a `app/config/.env`:

```dotenv
BEE_ENABLE_EXAMPLE_ROUTES=true
```

Al habilitarlas están disponibles estas rutas:

```text
GET           /api/examples/articles
GET           /api/examples/articles/{id}
POST          /api/examples/articles
PUT|PATCH     /api/examples/articles/{id}
DELETE        /api/examples/articles/{id}
```

La interfaz web del CRUD está disponible en:

```text
GET /examples/articles
GET /examples/article/{slug}
```

Esta página consume los endpoints anteriores mediante `fetch`, `async/await` y formularios codificados como `URLSearchParams`. Incluye creación, listado paginado, filtro por estado, edición, eliminación, estados de carga y presentación segura de errores JSON. El JavaScript se encuentra en `assets/js/examples/articlesCrud.js` y genera contenido dinámico con `textContent` para evitar inyectar HTML recibido desde la API.

El slug se genera automáticamente desde el título cuando no se envía uno. Si ya existe, el modelo añade sufijos incrementales (`mi-articulo-2`, `mi-articulo-3`). Los títulos y el botón `Ver` del listado enlazan a `/examples/article/{slug}`; ambos aparecen únicamente para artículos con estado `published`. La página responde con HTTP 404 cuando el slug no existe o sigue como borrador.

Los archivos del ejemplo usan caracteres UTF-8 reales, sin entidades HTML ni escapes Unicode para los acentos. Una prueba automática detecta texto doblemente codificado antes de publicar cambios.

### Identidad visual y estilos

El tema Bee es un bundle autónomo: compila Bootstrap 5 desde `assets/scss/bootstrap/` y, a continuación, incorpora tokens, base tipográfica, layout, componentes, páginas y utilidades propias. Para `CSS_FRAMEWORK=bs5` o `bs` no se descarga otra hoja de Bootstrap, por lo que las variables Sass controlan realmente todos sus componentes y se evita CSS duplicado.

`assets/scss/_theme-settings.scss` es el único punto de personalización público. Ahí se definen tanto las variables Sass como las custom properties Bee; no existe una segunda capa de tokens. Pueden modificarse paleta semántica, tipografía, espaciado, contenedores, bordes, radios, sombras, formularios, botones, tarjetas, navegación, tablas y opciones de compilación sin editar las fuentes de `assets/scss/bootstrap/`.

El orden de compilación de `assets/scss/main.scss` es deliberado:

1. Funciones de Bootstrap.
2. Variables configurables del tema Bee.
3. Componentes y utilidades de Bootstrap.
4. Capas visuales propias de Bee.

Para compilar el CSS minificado:

```bash
npx sass assets/scss/main.scss assets/css/main.min.css --style=compressed --no-source-map
```

Las plantillas cargan exclusivamente `assets/css/main.min.css`; los cambios en parciales nunca deben editar directamente el archivo compilado. Los avisos de deprecación que pueda emitir Sass proceden de la sintaxis interna de Bootstrap 5.3 y no impiden generar el bundle.

Las vistas locales incluidas con `beeController` comparten esta identidad visual: inicio, generador de contraseñas, perfil, diagnósticos, demos de Vue y Twig, así como las pantallas generales de error. Sus estilos viven en `assets/scss/_bee.scss`; las demos conservan sus puntos de montaje (`#mainApp` y `#testApp`) y los valores variables se escapan antes de renderizarse.

La navegación y el footer compartidos también usan componentes Bee adaptables, estados de ruta activos, enlaces de herramientas y datos de versión. La regla web de `.htaccess` reserva `/bee` y `/bee/*` para `beeController`, aunque exista el ejecutable CLI `bee` en la raíz; esto no altera el uso de `php bee <comando>` desde terminal.

### Renderizado de vistas

Los controladores tradicionales conservan `setTitle()`, `setData()`, `addToData()`, `setView()`, `setEngine()` y `render()`. El renderizador valida que la plantilla permanezca dentro de la carpeta del controlador, utiliza excepciones específicas en lugar de terminar el proceso con `die()` y mantiene `$d` para las vistas PHP.

El nuevo método `renderToString()` permite integrar el mismo flujo con respuestas del router moderno:

```php
final class articlesController extends Controller
{
    public function index(): string
    {
        $this->setTitle('Artículos');
        $this->setView('index');
        $this->addToData('articles', exampleArticleModel::all());

        return $this->renderToString();
    }
}
```

También está disponible `View::renderToString($view, $data, $engine, $controller)`. Twig utiliza UTF-8 y escape HTML automático; el filtro `raw` debe reservarse para contenido confiable. `View::render()` y `View::render_twig()` continúan emitiendo directamente para mantener compatibilidad.

Cuando las vistas viven en una carpeta distinta a la inferida desde el nombre del controlador, puede declararse con `$this->setViewDirectory('examples/articles')`. `exampleArticlePageController` muestra el flujo completo: extiende `Controller`, recibe `Configuration` por inyección, establece título, vista y datos, y retorna el HTML mediante `renderToString()`; sus plantillas consumen `$d` y reutilizan header, navbar y footer.

### Bee Creator y generación por CLI

Creator dispone de una interfaz local renovada para generar controladores modernos o legacy, vistas PHP/Twig y modelos ORM con campos fillable, casts y timestamps. También muestra todas las rutas cargadas; las rutas creadas desde la interfaz se administran de forma segura en `app/routes/creator.json`, sin reescribir `web.php` o `api.php`.

El constructor visual de modelos permite añadir columnas una por una y seleccionar su cast desde una lista, revisar la estructura resultante y retirar columnas antes de generar el archivo. El editor de rutas incluye restricciones habituales para parámetros —numérico, letras, alfanumérico, slug y UUID— además de una expresión personalizada cuando sea necesaria.

La misma API de generación se utiliza desde terminal:

```bash
php bee create:controller Articles --view
php bee create:controller Reports --type=legacy --view
php bee create:model Article --table=articles --fields=title:string,views:int,metadata:json
php bee create:view articles/show
```

Creator nunca sobrescribe archivos existentes. Los casts admitidos son `string`, `int`, `float`, `bool`, `array` y `json`; las rutas del manifiesto se validan antes de entrar al router y las rutas declaradas directamente en PHP permanecen de solo lectura desde la interfaz.

Ejemplo de consulta:

```php
$published = exampleArticleModel::published()
    ->orderBy('created_at', 'desc')
    ->paginate(10, 1);
```

El ejemplo está separado del núcleo y puede copiarse, adaptarse o eliminarse sin modificar Bee Framework.

Las excepciones producidas en rutas `/api`, solicitudes que aceptan `application/json` o rutas con middleware `api` se responden como JSON. En producción se ocultan los detalles internos; con `APP_DEBUG=true` se incluye el mensaje y el tipo de excepción para diagnóstico.

### Actualizaciones seguras

El inspector de paquetes valida manifiestos, compatibilidad, hashes y firmas Ed25519 mediante Sodium antes de aceptar una actualización. La instalación permanece separada de la inspección para reducir efectos laterales y permitir su uso desde HTTP o CLI.

### Verificación

```bash
php tests/Core/services.php
php tests/Core/configuration_access.php
php tests/Core/sanitizers.php
php tests/Core/bee_model.php
php tests/Core/modern_examples.php
php tests/Core/routing.php
php tests/Core/route_facade.php
php tests/Core/route_dispatcher.php
php tests/Core/modern_route_integration.php
php tests/Core/legacy_route_fallback.php
php tests/Updater/run.php
php bee core:status
composer --working-dir=app audit
```

## Changelog
### v 1.6.0

- Sidebar administrativa con contraste reforzado, iconos miel, estados activo/hover/focus claramente diferenciados y etiqueta semántica de sección.
- Nuevo backend administrativo Bee sin SB Admin 2: shell responsive Bootstrap 5, dashboard útil, navegación extensible y gestor seguro de usuarios preparado para sumar nuevos CRUDs.
- La pantalla de ingreso abandona SB Admin 2 y Bootstrap 4: ahora utiliza exclusivamente el tema Bee, componentes Bootstrap 5, campos accesibles y un layout responsive independiente.
- `bee/info` presenta un diagnóstico agrupado y adaptable con resumen de runtime, navegación por secciones y secretos siempre ocultos.
- El selector de tipo de controlador en Creator mejora contraste, lectura, adaptación móvil y estados seleccionado/focus con identidad Bee.
- Las tarjetas de “Tu espacio de trabajo” conservan su presentación limpia en hover mediante utilidades nativas de Bootstrap 5.
- Tema simplificado a una sola configuración y personalidad Bee aplicada a enlaces, botones, tabs, nav-pills y badges nativos de Bootstrap.
- Tema Bee autónomo y escalable: Bootstrap 5 se compila localmente, `_theme-settings.scss` centraliza la personalización y `main.min.css` deja de depender del CSS de Bootstrap servido por CDN.
- Creator prioriza componentes y utilidades Bootstrap 5, incorpora un constructor visual de columnas y presets estandarizados para parámetros de ruta.
- Bee Creator rediseñado con controladores modernos/legacy, modelos ORM configurables, vistas, rutas administradas y comandos CLI `create:*` reutilizando una API común.
- El CRUD web de artículos ahora demuestra el flujo estándar de `Controller` y `View`, incluyendo carpeta de vistas configurable e includes compartidos.
- Renderizador de vistas reutilizable y seguro con `renderToString()`, excepciones específicas, rutas protegidas y autoescape de Twig.
- Compatibilidad simultánea entre las rutas web de `beeController` y el ejecutable CLI `bee`, más navbar y footer renovados.
- Rediseño integral y adaptable de las vistas locales de `beeController`, navegación Bee, herramientas, demos, diagnóstico y errores.
- Núcleo compartido con bootstraps independientes para HTTP, CLI, cron, pruebas y updater.
- Configuración tipada, servicios reutilizables, logging estándar y manejo centralizado de errores.
- Autoload PSR-4 para componentes nuevos, manteniendo constantes y APIs globales como compatibilidad temporal.
- Router moderno con fachada `Route`, verbos HTTP, parámetros, nombres, grupos, middleware y fallback al router heredado.
- Comando CLI de diagnóstico y base segura para el sistema de actualizaciones.
- Documentación de uso actualizada para el núcleo, el router, CLI y actualizaciones.

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
