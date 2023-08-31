<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<!-- Bloque de documentación general -->
<section class="bg-light py-5 main-wrapper">
  <div class="container py-5">
    <div class="row">
      <div class="col-12 col-md-3">
        <div class="list-group sticky-top" style="top: 25px;" id="scrollSpyDoc">
          <a href="<?php echo new_anchor('instalacion'); ?>" class="list-group-item list-group-item-action">Instalación</a>
          <a href="<?php echo new_anchor('prepros'); ?>" class="list-group-item list-group-item-action">Prepros</a>
          <a href="<?php echo new_anchor('db'); ?>" class="list-group-item list-group-item-action">Base de datos</a>
          <a href="<?php echo new_anchor('routing'); ?>" class="list-group-item list-group-item-action">Routing</a>
          <a href="<?php echo new_anchor('modelos'); ?>" class="list-group-item list-group-item-action">Modelos</a>
          <a href="<?php echo new_anchor('coreFunc'); ?>" class="list-group-item list-group-item-action">Funciones del core</a>
          <a href="<?php echo new_anchor('customFunc'); ?>" class="list-group-item list-group-item-action">Funciones personalizadas</a>
          <a href="<?php echo new_anchor('clasesIncorporadas'); ?>" class="list-group-item list-group-item-action">Clases incorporadas</a>
          <a href="<?php echo new_anchor('hookList'); ?>" class="list-group-item list-group-item-action">Ganchos o hooks</a>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <?php echo get_module('bee/doc'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once INCLUDES . 'footer.php'; ?>