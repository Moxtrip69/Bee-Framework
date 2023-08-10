<?php require_once INCLUDES . 'inc_header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<main>

  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Tienda de ejemplo</h1>
        <p class="lead text-body-secondary">Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa doloribus quae maiores temporibus quam tenetur ratione eum itaque omnis officia id qui officiis nobis voluptates sit asperiores, sint voluptatem tempora!</p>
        <p>
          <a href="carrito" class="btn btn-primary my-2 me-2">Ver el carrito</a>
          <a href="admin" class="btn btn-secondary my-2">Ir al Dashboard</a>
        </p>
      </div>
    </div>
  </section>

  <div class="album py-5 bg-body-tertiary">
    <div class="container">
      <?php echo Flasher::flash(); ?>

      <?php if (!empty($d->products->rows)) : ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
          <?php foreach ($d->products->rows as $p) : ?>
            <div class="col">
              <div class="card shadow-sm">
                <div class="product-feed-image aspect-ratio-16-9">
                  <a href="<?php echo sprintf('tienda/producto/%s', $p->slug); ?>">
                    <?php if (is_file(UPLOADS . $p->image)) : ?>
                      <img src="<?php echo get_uploaded_image($p->image); ?>" alt="<?php echo $p->name; ?>" class="card-img-top">
                    <?php else : ?>
                      <img src="<?php echo get_image('broken.png'); ?>" alt="<?php echo $p->name; ?>" class="card-img-top ">
                    <?php endif; ?>
                  </a>
                </div>
                <div class="card-body">
                  <p class="card-text fw-bold"><?php echo $p->name; ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <a href="<?php echo build_url(sprintf('carrito/agregar/%s', $p->id), [], true, false); ?>" class="btn btn-sm btn-warning"><i class="fas fa-plus me-2"></i>Agregar al carrito</a>
                    </div>
                    <h3 class="text-body-secondary"><?php echo money($p->price < $p->compare_price ? $p->price : $p->compare_price); ?></h3>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="row">
          <div class="col-12">
            <?php echo $d->products->pagination; ?>
          </div>
        </div>
      <?php else : ?>
        <div class="row">
          <div class="col-12 py-5 text-center">
            <h3>No hay productos para mostrar</h3>
            <p class="text-muted">Lorem ipsum dolor sit amet.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

</main>

<?php require_once INCLUDES . 'inc_footer.php'; ?>