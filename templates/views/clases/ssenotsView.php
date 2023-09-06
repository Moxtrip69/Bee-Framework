<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<!-- Plantilla versión 1.0.5 -->
<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-6 offset-md-3 py-5">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3><?php echo $d->title; ?></h3>
          <button class="btn btn-sm btn-success" id="generarNot"><i class="fas fa-plus"></i></button>
        </div>
        <div class="card-body">
          <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
              <a class="navbar-brand" href="<?php echo get_base_url(); ?>"><img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" style="width: 100px;"></a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Cursos</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Precios</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notWrapper">
                      Notificaciones<i class="fas fa-bell fa-fw ms-2"></i>
                      <span class="position-absolute badge rounded-pill bg-danger" style="top: -5px; right: -5px;" id="notTotal">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="notList">
                      <li class="dropdown-item">Sin notificaciones.</li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>