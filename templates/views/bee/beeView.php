<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div id="test_ajax"></div>
<div id="test_api"></div>

<div class="main-wrapper">
  <section class="container py-5">
    <div class="row">
      <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
        <small class="d-block text-muted mb-3"><?php echo sprintf('Versión %s', get_bee_version()); ?></small>

        <h1 class="fw-bold">Un framework hecho en casa, con pasión y mucho cariño</h1>
        <h5 class="text-muted">Ligero, rápido y personalizable, úsalo como gustes, en tus proyectos personales o comerciales.</h5>

        <?php echo Flasher::flash(); ?>

        <div class="mt-3 wrapper_db_test" style="display: none;">
          <div class="alert"><!-- Ajax --></div>
        </div>

        <ul class="m-0 ps-4">
          <li class="mb-1">Desarrollado con <b>PHP, Javascript</b> y <b>HTML5</b></li>
          <li class="mb-1">Listo para <code>Bootstrap 5, Bulma y Foundation</code></li>
          <li class="mb-1">Funciona utilizando el patrón <b>MVC</b></li>
          <li class="mb-1">Sistema de sesiones de usuario persistentes con Cookies</li>
          <li class="mb-1"><b>ORM</b> sencillo incluido para manipulación de bases de datos</li>
          <li class="mb-1"><b>100%</b> personalizable y escalable</li>
        </ul>

        <a href="<?php echo 'https://github.com/Moxtrip69/Bee-Framework/tree/' . get_bee_version(); ?>" class="btn btn-success btn-lg mt-3 col-12 col-sm-6 col-md-6 col-lg-4" target="_blank">
          <i class="fas fa-download me-2"></i>Descargar
        </a>
      </div>
      <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
        <img src="<?php echo get_image('bee-framework-academia-de-joystick-roberto-orozco-aviles.png'); ?>" alt="<?php echo get_bee_name(); ?>" class="img-fluid" style="width: 80%;">
      </div>
    </div>
    <div class="row my-5">
      <div class="col-12 mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between alight-items-center">
          <div class="d-flex flex-column align-items-center mb-3">
            <i class="fas fa-book fs-1 text-warning mb-2"></i>
            <h3 class="fw-bold">Creator</h3>
            <p>Crea un nuevo controlador o modelo.</p>
            <a class="btn btn-light btn-sm" href="creator">Ver más</a>
          </div>

          <?php if (!Auth::validate()) : ?>
            <div class="d-flex flex-column align-items-center mb-3">
              <i class="fas fa-user fs-1 text-info mb-2"></i>
              <h3 class="fw-bold">Mi cuenta</h3>
              <p>Accede a la cuenta de pruebas.</p>
              <a class="btn btn-light btn-sm" href="login">Ingresar</a>
            </div>
          <?php else : ?>
            <div class="d-flex flex-column align-items-center mb-3">
              <i class="fas fa-user fs-1 text-info mb-2"></i>
              <h3 class="fw-bold">Mi cuenta</h3>
              <p>Mira la información de la cuenta actual.</p>
              <a class="btn btn-light btn-sm" href="bee/perfil">Mi cuenta</a>
            </div>
          <?php endif; ?>

          <div class="d-flex flex-column align-items-center mb-3">
            <i class="fab fa-vuejs fs-1 text-success mb-2"></i>
            <h3 class="fw-bold">Vue JS</h3>
            <p>Mira el ejemplo de integración.</p>
            <a class="btn btn-light btn-sm" href="vuejs">Ver más</a>
          </div>

          <div class="d-flex flex-column align-items-center mb-3">
            <i class="fab fa-github fs-1 mb-2"></i>
            <h3 class="fw-bold">Github</h3>
            <p>Sígueme en Github.</p>
            <a class="btn btn-light btn-sm" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.5">Ver más</a>
          </div>

          <div class="d-flex flex-column align-items-center mb-3">
            <i class="fas fa-play fs-1 text-danger mb-2"></i>
            <h3 class="fw-bold">El curso oficial</h3>
            <p>Mira cómo nació Bee framework.</p>
            <a class="btn btn-light btn-sm" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank">Ver curso</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Bloque de documentación general -->
  <section class="bg-light py-5">
    <div class="container py-5">
      <div class="row">
        <div class="col-12 col-md-3">
          <div class="list-group sticky-top" style="top: 25px;" id="scrollSpyDoc">
            <a class="list-group-item disabled" aria-disabled="true">Documentación</a>
            <a href="<?php echo new_anchor('instalacion'); ?>" class="list-group-item list-group-item-action">Instalación</a>
            <a href="<?php echo new_anchor('prepros'); ?>" class="list-group-item list-group-item-action">Prepros</a>
            <a href="<?php echo new_anchor('db'); ?>" class="list-group-item list-group-item-action">Base de datos</a>
            <a href="<?php echo new_anchor('routing'); ?>" class="list-group-item list-group-item-action">Routing</a>
            <a href="<?php echo new_anchor('modelos'); ?>" class="list-group-item list-group-item-action">Modelos</a>
            <a href="<?php echo new_anchor('coreFunc'); ?>" class="list-group-item list-group-item-action">Funciones del core</a>
            <a href="<?php echo new_anchor('customFunc'); ?>" class="list-group-item list-group-item-action">Funciones personalizadas</a>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="card shadow-sm" >
            <div class="card-body">
              <?php echo get_module('bee/doc'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>