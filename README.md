![Bee Framework](https://raw.githubusercontent.com/Moxtrip69/Bee-Framework/master/assets/images/bee_logo.png)
# Bee-Framework
Mini framework desarrollado por el equipo de Joystick SA de CV en México.
Puedes hacer uso de el para tus proyectos personales o comerciales, es ligero y fácil de implementar para proyectos tanto pequeños como aquellos que requieren escalabilidad y visión a futuro.

## Changelog
### v 1.1.0
- Hemos cambiado algunos archivos de configuración para facilitar la escalabilidad y soporte al código, separando las constantes de bases de datos y basepath a **bee_config.php** y creando **settings.php**, separando del resto para mejorar su encapsulamiento.
- Nuevas funciones añadidas para carga de información de usuario registrada en el payload de las variables de sesión.
- Mejoras realizadas en el creador de controladores y sus plantillas por defecto, para agilizar el flujo de trabajo.
- Se añadieron nuevos parametros a la clase **Db.php** para regresar solo regultados como **array asociativo**.
- Actualizamos el sistema para funcionar completamente con **Bootstrap 5 Beta**.
- Seguimos realizando mejoras y corrección de bugs que reporta la comunidad.
- Solucionamos un bug común en la función de enviar email, añadiendo simplemente el método **$mail->isSMTP()**, con esto funcionará sin problema alguno dependiendo la implementación requerida.
- Ahora el creador de controladores también genera de forma inmediata una carpeta con las vistas iniciales y métodos iniciales a utilizar en cualquier proyecto en general.

### Síguenos
Recuerda que tenemos contenido gratuito y excelentes scripts y sistemas listos para usar en nuestra [**Academia de Joystick**](https://www.joystick.com.mx), además de cursos en línea para que exprimas tu potencial al máximo.