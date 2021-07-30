<!-- scripts necessarios -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"></script>

<!-- toastr js -->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- waitme js -->
<script src="<?php echo PLUGINS.'waitme/waitMe.min.js'; ?>"></script>

<!-- Lightbox js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- CDN VUEJs for dev v3 -->
<?php if (is_local()): ?>
  <script src="https://unpkg.com/vue@next"></script>
<?php else: ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.0.11/vue.cjs.js"></script>
<?php endif; ?>

<!-- Objeto Bee Javascript registrado -->
<?php echo load_bee_obj(); ?>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS.'main.js?v='.get_version(); ?>"></script>
