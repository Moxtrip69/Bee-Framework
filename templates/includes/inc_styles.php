<!-- CSS Framework | Configurado en settings.php | defecto = Bootstrap 5 -->
<?php echo get_css_framework(); ?>

<!-- Font awesome 6 -->
<?php echo get_fontawesome(); ?>

<!-- Todo plugin adicional debe ir debajo de está línea -->

<!-- Toastr css -->
<?php echo get_toastr('styles'); ?>

<!-- Waitme css -->
<?php echo get_waitMe('styles'); ?>

<!-- Lightbox -->
<?php echo get_lightbox('styles'); ?>

<!-- CDN Vue js 3 | definido en settings.php -->
<?php echo get_vuejs(); ?>

<!-- Estilos registrados manualmente -->
<?php echo load_styles(); ?>

<!-- Three js -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>


<!-- Estilos personalizados deben ir en main.css o abajo de esta línea -->
<link rel="stylesheet" href="<?php echo CSS . 'main.css?v=' . get_asset_version(); ?>">
