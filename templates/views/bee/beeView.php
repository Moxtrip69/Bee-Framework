<?php require_once INCLUDES . 'inc_bee_header.php'; ?>
<?php require_once INCLUDES . 'inc_bee_navbar.php'; ?>

<div id="test_ajax"></div>

<div class="container py-5">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6 col-md-6 col-lg-6 col-12">
      <h2><a href="<?php echo URL; ?>"><img src="<?php echo get_bee_logo(); ?>" alt="Bee framework" class="img-fluid" style="width: 100px;"></a></h2>

      <small class="d-block text-muted mb-3"><?php echo sprintf('Versión %s', get_bee_version()); ?></small>
      <p>Un framework hecho en casa, con pasión y mucho cariño.</p>
      <p>Ligero, rápido y personalizable. Úsalo como gustes, en tus proyectos personales o comerciales.</p>

      <ul class="list-group">
        <li class="list-group-item">Desarrollado con <b>PHP, Javascript</b> y <b>HTML5</b></li>
        <li class="list-group-item">Listo para <code>Bootstrap 5, Bulma y Foundation</code></li>
        <li class="list-group-item">Funciona utilizando el patrón <b>MVC</b></li>
        <li class="list-group-item">Sistema de sesiones de usuario persistentes con Cookies</li>
        <li class="list-group-item"><b>ORM</b> sencillo incluido para manipulación de bases de datos</li>
        <li class="list-group-item"><b>100%</b> personalizable y escalable <?php echo more_info('Soy un Tooltip de Bootstrap 5'); ?></li>
        <li class="list-group-item">Aprende cómo se hizo <code>Bee Framework</code> desde 0 <a class="btn btn-sm btn-success text-decoration-none" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank">Ver más</a></li>
      </ul>

      <div class="mt-3 wrapper_db_test" style="display: none;">
        <div class="alert"></div>
      </div>

      <div class="mt-5">
        <h3>¿Qué buscas hacer?</h3>
        <ul class="list-group list-group-horizontal-sm mb-3">
          <li class="list-group-item">
            <p>Crea un nuevo Controlador o Modelo.</p>
            <a class="btn btn-warning btn-sm" href="creator"><i class="fas fa-file fa-fw"></i> Creator</a>
          </li>
          <?php if (!Auth::validate()) : ?>
            <li class="list-group-item">
              <p>Accede a la cuenta de pruebas.</p>
              <a class="btn btn-warning btn-sm" href="login"><i class="fas fa-fingerprint fa-fw"></i> Ingresar</a>
            </li>
          <?php else : ?>
            <li class="list-group-item">
              <p>Mira la información de la cuenta actual.</p>
              <a class="btn btn-info btn-sm" href="bee/perfil"><i class="fas fa-user fa-fw"></i> Mi cuenta</a>
            </li>
          <?php endif; ?>
        </ul>
        <ul class="list-group list-group-horizontal-sm mb-3">
          <li class="list-group-item">
            <p>Mira el ejemplo de integración de Vuejs con Bee framework.</p>
            <a class="btn btn-success btn-sm" href="vuejs"><i class="fab fa-vuejs fa-fw"></i> Vue JS</a>
          </li>
          <li class="list-group-item">
            <p>Síguenos en Github.</p>
            <a class="btn btn-dark btn-sm" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.5"><i class="fab fa-github fa-fw"></i> Github</a>
          </li>
          <li class="list-group-item">
            <p>Mira cómo nació Bee framework.</p>
            <a class="btn btn-primary btn-sm" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank"><i class="fas fa-book fa-fw"></i> Ver curso</a>
          </li>
        </ul>
      </div>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-12 text-center">
      <img src="<?php echo get_image('bee.png'); ?>" alt="<?php echo get_bee_name(); ?>" class="img-fluid mt-5 bee_fly" style="width: 70%; margin: auto auto;">
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'inc_bee_footer.php'; ?>