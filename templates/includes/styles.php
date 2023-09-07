<!-- CSS Framework | Configurado en settings.php | defecto = Bootstrap 5 -->
<?php echo get_css_framework(); ?>

<!-- Font awesome 6 -->
<?php echo get_fontawesome(); ?>

<!-- Todo plugin adicional debe ir debajo de está línea -->

<!-- Sweet alert 2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css">

<!-- Toastr css -->
<?php echo get_toastr('styles'); ?>

<!-- Waitme css -->
<?php echo get_waitMe('styles'); ?>

<!-- Lightbox -->
<?php echo get_lightbox('styles'); ?>

<!-- CDN Vue js 3 | definido en settings.php -->
<?php echo get_vuejs(); ?>

<!-- Estilos personalizados deben ir en main.css o abajo de esta línea -->
<link rel="stylesheet" href="<?php echo CSS . 'main.css?v='.get_asset_version(); ?>">

<!-- Estilos registrados manualmente -->
<?php echo load_styles(); ?>