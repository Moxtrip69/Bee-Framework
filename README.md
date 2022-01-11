![Bee Framework](https://raw.githubusercontent.com/Moxtrip69/Bee-Framework/master/assets/images/bee_logo.png)
# Bee-Framework
Mini framework desarrollado por el equipo de Joystick SA de CV en México.
Puedes hacer uso de el para tus proyectos personales o comerciales, es ligero y fácil de implementar para proyectos tanto pequeños como aquellos que requieren escalabilidad y visión a futuro.

## Changelog
### v 1.5.0
- Se ha sustituido el controlador por defecto de **homeController** a **beeController** esto para facilitar el actualizado del core a nuevas versiones una vez en producción, facilitando el trabajo de los desarrolladores que lo utilizan.
- Se ha implementado el uso de Vue.js 3 con base a **CDN** y no CLI, puede ser removido retirando la etiqueda en el archivo **inc_styles.php**.
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
- Se han separado las hojas de estilos incrustadas en el header al archivo **inc_styles.php** y los scripts al archivo **inc_scripts.php** para facilitar la reutilización de los mismos.
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