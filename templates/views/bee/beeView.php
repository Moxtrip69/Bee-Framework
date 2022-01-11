<?php require_once INCLUDES.'inc_bee_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

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
        <li class="list-group-item">Listo para <code>Bootstrap 5 | Bulma | Foundation</code></li>
        <li class="list-group-item">Funciona utilizando el patrón <b>MVC</b></li>
        <li class="list-group-item">Sistema de sesiones de usuario listas para usarse</li>
        <li class="list-group-item"><b>ORM</b> sencillo incluido para manipulación de bases de datos</li>
        <li class="list-group-item"><b>100%</b> personalizable y escalable <?php echo more_info('¡Hola mundo!'); ?></li>
        <li class="list-group-item">Aprende como se hizo el framework <span class="badge bg-success"><a class="text-decoration-none text-white" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank"><i class="fas fa-link fa-fw"></i> aquí</a></span></li>
      </ul>

      <div class="mt-3 wrapper_db_test" style="display: none;">
        <div class="alert"></div>
      </div>

      <div class="mt-5">
        <a class="btn btn-dark btn-sm mt-1" href="creator"><i class="fas fa-file fa-fw"></i> Creator</a>
        <a class="btn btn-warning btn-sm mt-1" href="login"><i class="fas fa-fingerprint fa-fw"></i> Ingresar</a>
        <a class="btn btn-info btn-sm text-white mt-1" href="bee/perfil"><i class="fas fa-user fa-fw"></i> Mi cuenta</a>
        <a class="btn btn-success btn-sm mt-1" href="vuejs"><i class="fab fa-vuejs fa-fw"></i> Vue JS</a>
        <a class="btn btn-success btn-sm mt-1" href="https://github.com/Moxtrip69/Bee-Framework"><i class="fab fa-github fa-fw"></i> Github</a>
        <a class="btn btn-success btn-sm mt-1" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank"><i class="fas fa-book fa-fw"></i> ¿Buscas el curso?</a>
      </div>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-12 text-center">
      <img src="<?php echo get_image('bee.png'); ?>" alt="<?php echo get_bee_name(); ?>" class="img-fluid mt-5 bee_fly" style="width: 70%; margin: auto auto;">
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>