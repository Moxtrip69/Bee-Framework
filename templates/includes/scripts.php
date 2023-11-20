<!-- jQuery | definido en settings.php -->
<?php echo get_jquery(); ?>

<!-- jQuery easing -->
<?php echo get_jquery_easing(); ?>

<!-- CSS Framework scripts | Por defecto Bootstrap 5 | definido en settings.php -->
<?php echo get_css_framework_scripts(); ?>

<!-- Axios | definido en settings.php -->
<?php echo get_axios(); ?>

<!-- SweetAlert2 -->
<?php echo get_sweetalert2(); ?>

<!-- Toastr js -->
<?php echo get_toastr(); ?>

<!-- waitMe js -->
<?php echo get_waitMe(); ?>

<!-- Lightbox js -->
<?php echo get_lightbox(); ?>

<!-- Objeto Bee Javascript registrado -->
<?php echo load_bee_obj(); ?>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS . 'main.min.js?v=' . get_asset_version(); ?>"></script>