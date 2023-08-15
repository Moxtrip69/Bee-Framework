<!-- jQuery | definido en settings.php -->
<?php echo get_jquery(); ?>

<!-- Core plugin JavaScript-->
<?php echo get_jquery_easing(); ?>

<!-- Bundle de Bootstrap 4 -->
<script src="<?php echo JS . 'admin/bootstrap.min.js'; ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

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

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS . 'main.min.js?v=' . get_asset_version(); ?>"></script>

<!-- Chartjs -->
<script src="<?php echo JS . 'admin/Chart.min.js'; ?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo JS . 'admin/sb-admin-2.min.js'; ?>"></script>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>
</body>

</html>