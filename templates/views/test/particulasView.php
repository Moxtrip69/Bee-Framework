<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<!-- ======= Hero Section ======= -->
<section id="hero" class="overflow-hidden position-relative" style="background: #FFF3CD;">
  <div id="particles-js" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; overflow: hidden;"></div>  
  <div class="container py-5">
    <div class="row flex-lg-row-reverse align-items-center py-5">
      <div class="col-12 col-lg-6">
        <img src="https://cdn-themes.thinkific.com/410288/375889/rNYO3Nq3QLATtmpHkOZp_js_460.png" class="d-block mx-lg-auto img-fluid" alt="{{ bundle.name }}">
      </div>
      <div class="col-12 col-lg-6">
        <h1 class="display-5 fw-bold lh-1 mb-3">De cero a experto en desarrollo web</h1>
        <p class="lead">Aprende programación web de forma profesional y a tu propio ritmo con proyectos reales.</p>
        
        <ul class="m-0 mb-3 p-0">
          <li class="mb-2"><i class="fa fa-check fa-fw text-success"></i> Accede a +25 cursos profesionales en línea.</li>
          <li class="mb-2"><i class="fa fa-check fa-fw text-success"></i> Genera más ingresos o inicia tu propio negocio.</li>
          <li class="mb-2"><i class="fa fa-check fa-fw text-success"></i> Te ayudaré a mejorar tu carrera profesional.</li>
        </ul>

        <div class="d-grid gap-2 d-md-flex justify-content-md-start" >
          <a 
            href="#" 
            class="mt-3 btn btn-success"
            data-course_id="{{ bundle.id }}" 
            data-redirect="1"
            id="ajsMainCTA" 
            style="z-index: 2;"
          >
            Comprar ahora
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Hero -->

<?php require_once INCLUDES . 'footer.php'; ?>