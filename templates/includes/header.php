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

  <!-- styles.php -->
  <?php require_once INCLUDES . 'styles.php'; ?>

  <!-- Carga de meta tags -->
  <?php echo get_page_og_meta_tags(); ?>
  
  <!-- Carga de Meta Pixel -->
  <?php echo init_meta_pixel(); ?>
</head>

<body>
<!-- ends header.php -->