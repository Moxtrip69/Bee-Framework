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

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS . 'main.min.js?v=' . get_asset_version(); ?>"></script>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>

<!-- Ejemplo para carrito -->
<script>
  // Example starter JavaScript for disabling form submissions if there are invalid fields
  (() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>