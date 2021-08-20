<!-- jQuery -->
<?php echo get_jquery(); ?>

<!-- CSS Framework scripts | Por defecto Bootstrap 5 | settings.php -->
<?php echo get_css_framework_scripts(); ?>

<!-- Toastr js -->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- waitme js -->
<script src="<?php echo PLUGINS.'waitme/waitMe.min.js'; ?>"></script>

<!-- Lightbox js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- Objeto Bee Javascript registrado -->
<?php echo load_bee_obj(); ?>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS.'main.js?v='.get_version(); ?>"></script>
