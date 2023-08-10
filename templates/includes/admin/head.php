<!DOCTYPE html>
<html lang="<?php echo get_site_lang(); ?>">

<head>
  <!-- Agregar basepath para definir a partir de donde se deben generar los enlaces y la carga de archivos -->
  <base href="<?php echo get_basepath(); ?>">

  <!-- Charset del sitio -->
  <meta charset="<?php echo get_site_charset(); ?>">

  <!-- Título general del sitio -->
  <title><?php echo isset($d->title) ? $d->title . ' - ' . get_sitename() : 'Bienvenido - ' . get_sitename(); ?></title>

  <!-- Meta viewport requerido para responsividad -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Retro compatibilidad con internet explorer / edge -->
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- Favicon del sitio -->
  <?php echo get_favicon(); ?>

  <!-- SB Admin 2 CSS y fuentes -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="<?php echo CSS . 'admin/sb-admin-2.min.css'; ?>" rel="stylesheet">

  <!-- CSS Framework | Configurado en settings.php | defecto = Bootstrap 5 -->
  <?php echo get_css_framework(); ?>

  <!-- Font awesome 6 -->
  <?php echo get_fontawesome(); ?>

  <!-- Toastr css -->
  <?php echo get_toastr('styles'); ?>

  <!-- Waitme css -->
  <?php echo get_waitMe('styles'); ?>

  <!-- Lightbox -->
  <?php echo get_lightbox('styles'); ?>

  <!-- CDN Vue js 3 | definido en settings.php -->
  <?php echo get_vuejs(); ?>

  <!-- Estilos personalizados deben ir en main.css o abajo de esta línea -->
  <link href="<?php echo CSS . 'main.css?v=' . get_asset_version(); ?>" rel="stylesheet">
  
  <!-- Estilos registrados manualmente -->
  <?php echo load_styles(); ?>

  <!-- Carga de meta tags -->
  <?php echo get_page_og_meta_tags(); ?>
</head>