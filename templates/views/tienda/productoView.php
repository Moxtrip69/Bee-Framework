<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<div class="container my-5">
  <div class="row">
    <div class="col-md-5">
      <div class="main-img">
        <img class="img-fluid rounded" src="<?php echo get_uploaded_image($d->p->imagen); ?>" alt="<?php echo $d->p->nombre; ?>">
        <div class="row my-3 previews">
          <?php foreach ([1,2,3,4] as $img): ?>
            <div class="col-md-3">
              <img class="img-fluid h-100 rounded" src="<?php echo get_uploaded_image($d->p->imagen); ?>" alt="<?php echo $d->p->nombre; ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="main-description px-2">
        <div class="text-primary text-uppercase text-bold">
          Categoría de ejemplo
        </div>
        <div class="product-title text-bold my-3">
          <?php echo $d->p->nombre; ?>
        </div>

        <div class="price-area my-4">
          <?php if ($d->p->precio < $d->p->precio_comparacion): ?>
            <?php $discount = round(100 - (100 * $d->p->precio / $d->p->precio_comparacion)); ?>
            <p class="old-price mb-1"><del><?php echo money($d->p->precio_comparacion) ?></del> <span class="text-danger"><?php echo sprintf('(%s%% OFF)', $discount); ?></span></p>
            <p class="new-price text-bold mb-1"><?php echo money($d->p->precio); ?></p>
          <?php else: ?>
            <p class="new-price text-bold mb-1"><?php echo money($d->p->precio); ?></p>
          <?php endif; ?>
          <p class="text-secondary mb-1">(Impuestos incluidos)</p>
        </div>

        <?php echo Flasher::flash(); ?>

        <div class="buttons d-flex my-5">
          <div class="block">
            <a href="#" class="shadow btn btn-primary"><i class="fas fa-heart me-2"></i>Lista de deseos</a>
          </div>
          <div class="block">
            <a href="<?php echo build_url(sprintf('carrito/agregar/%s', $d->p->id), [], true, false); ?>" class="shadow btn btn-warning"><i class="fas fa-plus me-2"></i>Agregar al carrito</a>
          </div>

          <div class="block quantity">
            <input type="number" class="form-control" id="cart_quantity" step="1" value="1" min="0" max="5" placeholder="1" name="cart_quantity">
          </div>
        </div>
      </div>

      <div class="product-details my-4">
        <p class="details-title text-color mb-1">Detalles del producto</p>
        <p class="description"><?php echo $d->p->descripcion; ?></p>
      </div>

      <div class="row questions bg-light p-3">
        <div class="col-md-1 icon">
          <i class="fa-brands fa-rocketchat text-primary"></i>
        </div>
        <div class="col-md-11 text">
          ¿Tienes alguna pregunta acerca de nuestros productos? No dudes en ponerte en contacto con nuestros representantes a través del chat en vivo o por correo electrónico. ¡Estamos aquí para ayudarte!
        </div>
      </div>

      <div class="delivery my-4">
        <p class="font-weight-bold mb-0"><span><i class="fa-solid fa-truck"></i></span> <b>La entrega se realiza en 3 días a partir de la fecha de compra.</b> </p>
        <p class="text-secondary">Ordena ahora para recibir la entrega de este producto.</p>
      </div>
      <div class="delivery-options my-4">
        <p class="font-weight-bold mb-0"><span><i class="fa-solid fa-filter"></i></span> <b>Opciones de envío</b> </p>
        <p class="text-secondary">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod, corporis.</p>
      </div>
    </div>
  </div>
</div>
<hr>

<div class="container similar-products my-4">
  <p class="display-5">Productos similares</p>

  <div class="row">
    <div class="col-md-3">
      <div class="similar-product">
        <img class="w-100" src="https://source.unsplash.com/gsKdPcIyeGg" alt="Preview">
        <p class="title">Lovely black dress</p>
        <p class="price">$100</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="similar-product">
        <img class="w-100" src="https://source.unsplash.com/sg_gRhbYXhc" alt="Preview">
        <p class="title">Lovely Dress with patterns</p>
        <p class="price">$85</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="similar-product">
        <img class="w-100" src="https://source.unsplash.com/gJZQcirK8aw" alt="Preview">
        <p class="title">Lovely fashion dress</p>
        <p class="price">$200</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="similar-product">
        <img class="w-100" src="https://source.unsplash.com/qbB_Z2pXLEU" alt="Preview">
        <p class="title">Lovely red dress</p>
        <p class="price">$120</p>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>