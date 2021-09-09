<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>

<div id="test_ajax"></div>

<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-8 text-center offset-xl-2 py-5">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_image('bee_logo.png') ?>" alt="Bee framework" class="img-fluid" style="width: 200px;"></a>
      <h2 class="mt-5"><span class="text-warning">Bee</span> framework</h2>
      <small class="d-block text-muted mb-3"><?php echo sprintf('Versión %s', get_bee_version()); ?></small>
      <p class="text-center">Un framework hecho en casa, con pasión y mucho cariño.</p>
      <p>Ligero, rápido y personalizable. Úsalo como gustes, en tus proyectos personales o comerciales.</p>
      
      <ul class="">
        <li>Desarrollado con <b>PHP, Javascript y HTML5</b></li>
        <li>Listo para <code>Bootstrap 5 | Bulma | Foundation</code></li>
        <li>Funciona utilizando el patrón <b>MVC</b></li>
        <li>Sistema de sesiones de usuario listas para usarse</li>
        <li>ORM sencillo incluido para manipulación de bases de datos</li>
        <li><b>100%</b> personalizable y escalable <?php echo more_info('¡Hola mundo!'); ?></li>
      </ul>

      <div class="mt-5">
        <a class="btn btn-light btn-sm" href="creator"><i class="fas fa-plus fa-fw"></i> Creator</a>
        <a class="btn btn-warning btn-sm" href="login"><i class="fas fa-fingerprint fa-fw"></i> Ingresar</a>
        <a class="btn btn-info btn-sm" href="home/flash"><i class="fas fa-user fa-fw"></i> Mi cuenta</a>
        <a class="btn btn-success btn-sm" href="home/vue"><i class="fab fa-vuejs fa-fw"></i> Vue JS</a>
        <a class="btn btn-success btn-sm" href="https://github.com/Moxtrip69/Bee-Framework"><i class="fab fa-github fa-fw"></i> Github</a>
        <a class="btn btn-success btn-sm" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql?coupon=hotsale2021" target="_blank"><i class="fas fa-book fa-fw"></i> ¿Buscas el curso?</a>
      </div>

      <div class="mt-5 justify-content-center">
        <p class="text-muted">Desarrollado con <i class="fas fa-heart text-danger"></i> por <a href="https://www.academy.joystick.com.mx">Joystick</a>.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>