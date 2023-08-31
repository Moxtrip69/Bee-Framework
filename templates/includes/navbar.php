<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="<?php echo get_base_url(); ?>">
      <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" width="100px">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?php echo get_base_url(); ?>">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tienda">Tienda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link position-relative" href="carrito"><i class="fas fa-shopping-cart fa-fw"></i>
            <span class="position-absolute start-100 translate-middle badge rounded-pill bg-danger d-none d-lg-inline" style="top: 10px;">
              <?php echo $d->cart->totalItems; ?>
              <span class="visually-hidden">Carrito de compras</span>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main_wrapper" style="min-height: 95vh;">