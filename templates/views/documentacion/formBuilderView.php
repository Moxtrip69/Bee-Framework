<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<!-- Bloque de documentación general -->
<section class="bg-light py-5 main-wrapper">
  <div class="container py-5">
    <div class="row">
      <div class="col-12 offset-md-3 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4># Uso rápido</h4>
            <p>Es una clase incorporada desde la versión <code>1.5.8</code> para la generación de formularios de forma rápida y dinámica, soportando la mayoría de inputs HTML5.</p>
            <p>Para crear un formulario basta con instanciar nuestra clase <code>BeeFormBuilder</code> y esta requiere 6 parámetros: <code>$name, $id, $classes, $action, $post, $sendFiles</code></p>
            <?php echo code_block(
'$form = new BeeFormBuilder(
  $name,     // nombre del formulario, anexado en el atributo data-name
  $id,       // id del formulario
  $classes,  // clases añadidas
  $action,   // ruta o archivo que procesará la solicitud
  $post,     // true si será petición POST o false si será GET
  $sendFiles // true si se enviarán archivos o false si no se enviarán
);') ?>

            <h4># Nuevos campos</h4>
            <p>Podrás insertar nuevos campos o inputs con los diferentes métodos disponibles en la clase, acepta la mayoría de inputs de HTML5 de forma sencilla y general, los parámetros son casi los mismos para los diferentes tipos de campos, sólo hay variaciones para algunos como los <code>select</code> o <code>checkbox</code> que requieren configuración adicional.</p>
            <p>Por ejemplo para añadir un simple campo de texto para el <code>username</code> podría ser así con el método <code>addTextField(...)</code>:</p>
            <?php echo code_block(
'$form->addTextField(
  \'username\',          // el atributo name del campo
  \'Nombre de usuario\', // la etiqueta
  [\'form-control\'],    // clases como array
  \'username\',          // el atributo id del campo
  true,                // requerido o no
  \'PanchoVilla\'        // valor por defecto
);') ?>

            <p>Por ejemplo para añadir el campo de <code>correo electrónico</code> del usuario usando el método <code>addEmailField(...)</code>:</p>
            <?php echo code_block(
'$form->addEmailField(
  \'email-usuario\',      // el atributo name del campo
  \'Correo electrónico\', // la etiqueta
  [\'form-control\'],     // clases como array
  \'emailUsuario\',       // el atributo id del campo
  true,                 // requerido o no
  \'pancho@villa.com\'    // valor por defecto
);') ?>

            <h4># Añadir botones</h4>
            <p>Para añadir el botón de submit o envío de información podemos usar el método <code>addButton(...)</code>:</p>
            <?php echo code_block(
'$form->addButton(
  \'submit\',             // el nombre del botón
  \'submit\',             // el typo de botón entre submit, reset y cancel
  \'Generar\',            // el código html dentro del botón
  [\'btn btn-success\'],  // clases del botón como array
  \'submit\'              // id del botón
);') ?>

            <h4># Renderizar el formulario</h4>
            <p>Ya por último sólo queda renderizar el formulario, para esto necesitamos procesar la información de todos los campos y generar su estructura HTML, usamos el método <code>getFormHtml()</code> que devuelve todo el código HTML del formulario listo para ser insertado en nuestra vista o donde sea requerido.</p>
            <?php echo code_block('$html = $form->getFormHtml(); // Código HTML listo para ser usado'); ?>

            <h4># Generar un script asíncrono</h4>
            <p><code>BeeFormBuilder</code> tiene incorporada la funcionalidad para generar un script para enviar la información del formulario usando <code>AJAX</code> con Javascript vanilla y <code>fetch()</code>, para esto basta con utilizar el método <code>generateFetchScript($url, $accessToken, $addEventListener)</code> y requiere 3 parámetros, el primero es obligatorio <code>$url</code> y representa la URL o script que procesará la información del formulario:</p>
            <?php echo code_block(
'$script = $form->generateFetchScript(
  "ajax/procesar-formulario", // la url o script
  "abcd-1234-efgh-5678",      // token de acceso a API
  true                        // true si queremos añadir un event listener por defecto o false para no agregarlo  
);'); ?>
            <p>Este método regresa un bloque de código HTML con Javascript <code><?php echo htmlspecialchars('<script>...</script>'); ?></code> listo para ser inyectado en tu sitio web.</p>

            <p>La forma en que el eventListener funciona es escuchando el evento <code>submit</code> del formulario, es decir se ejecutará el envío de nuestro formulario de manera asíncrona una vez que se presione el botón o elemento que active el evento submit.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once INCLUDES . 'footer.php'; ?>