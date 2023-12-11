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

        <div class="d-flex flex-row gap-2 mt-3">
          <a href="<?php echo 'https://github.com/Moxtrip69/Bee-Framework/tree/' . get_bee_version(); ?>" class="btn btn-success px-4" target="_blank">
            <i class="fas fa-download me-2"></i>Descargar
          </a>
          <a href="documentacion" class="btn btn-primary px-4">Documentación</a>
          <a href="<?php echo build_url('bee/upgrade-core'); ?>" class="btn btn-danger px-4 confirmar"><i class="fas fa-fw fa-refresh"></i> Actualizar core</a>
        </div>
      </div>
      <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
        <img src="<?php echo get_image('bee-framework-academia-de-joystick-roberto-orozco-aviles.png'); ?>" alt="<?php echo get_bee_name(); ?>" class="img-fluid" style="width: 80%;">
      </div>
    </div>
    <div class="row my-5">
      <div class="col-12 mt-5">
        <div class="row g-3">
          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fas fa-book fs-1 text-warning mb-2"></i>
              <h3 class="fw-bold">Creator</h3>
              <p>Crea un controlador, modelo o vista.</p>
              <a class="btn btn-light btn-sm" href="creator">Ver más</a>
            </div>
          </div>
          
          <div class="col-12 col-md-3">
            <?php if (!Auth::validate()) : ?>
              <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
                <i class="fas fa-user fs-1 text-info mb-2"></i>
                <h3 class="fw-bold">Mi cuenta</h3>
                <p>Accede a la cuenta de pruebas.</p>
                <a class="btn btn-light btn-sm" href="login">Ingresar</a>
              </div>
            <?php else : ?>
              <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
                <i class="fas fa-user fs-1 text-info mb-2"></i>
                <h3 class="fw-bold">Mi cuenta</h3>
                <p>Mira la información de la cuenta actual.</p>
                <a class="btn btn-light btn-sm" href="bee/perfil">Mi cuenta</a>
              </div>
            <?php endif; ?>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fab fa-vuejs fs-1 text-success mb-2"></i>
              <h3 class="fw-bold">Vue JS</h3>
              <p>Mira el ejemplo de integración.</p>
              <a class="btn btn-light btn-sm" href="bee/vuejs">Ver más</a>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fab fa-github fs-1 mb-2"></i>
              <h3 class="fw-bold">Github</h3>
              <p>Sígueme en Github.</p>
              <a class="btn btn-light btn-sm" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.5">Ver más</a>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fas fa-play fs-1 text-danger mb-2"></i>
              <h3 class="fw-bold">El curso oficial</h3>
              <p>Mira cómo nació Bee framework.</p>
              <a class="btn btn-light btn-sm" href="https://www.academy.joystick.com.mx/courses/crea-tu-propio-framework-profesional-mvc-con-php-poo-mysql" target="_blank">Ver curso</a>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fab fa-discord fs-1 text-discord mb-2"></i>
              <h3 class="fw-bold">Discord</h3>
              <p>Úneta gratis a la comunidad.</p>
              <a class="btn btn-light btn-sm" href="https://discord.gg/wTzhKrg" target="_blank">Unirme</a>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fab fa-whatsapp fs-1 text-success mb-2"></i>
              <h3 class="fw-bold">WhatsApp</h3>
              <p>Úneta gratis al grupo.</p>
              <a class="btn btn-light btn-sm" href="https://chat.whatsapp.com/GX86T4pVIFvCdMyovY5UgP" target="_blank">Unirme</a>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div class="d-flex flex-column align-items-center border rounded p-4 shadow">
              <i class="fas fa-heart fs-1 text-danger mb-2"></i>
              <h3 class="fw-bold">Donaciones</h3>
              <p>¿Me ayudarías?</p>
              <a class="btn btn-light btn-sm" href="https://bit.ly/aportar-un-poco" target="_blank">Donar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>