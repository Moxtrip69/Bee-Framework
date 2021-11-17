<!DOCTYPE html>
<html lang="<?php echo SITE_LANG; ?>">
<head>
  <!-- Agregar basepath para definir a partir de donde se deben generar los enlaces y la carga de archivos -->
  <base href="<?php echo BASEPATH; ?>">

  <!-- Charset del sitio -->
  <meta charset="<?php echo SITE_CHARSET; ?>">
  
  <!-- Título general del sitio -->
  <title><?php echo isset($d->title) ? $d->title.' - '.get_sitename() : 'Bienvenido - '.get_sitename(); ?></title>

  <!-- Meta viewport requerido para responsividad -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Retro compatibilidad con internet explorer / edge -->
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- Favicon del sitio -->
  <?php echo get_favicon(); ?>
  
  <!-- inc_styles.php -->
  <?php require_once INCLUDES.'inc_styles.php'; ?>
</head>

<body>
<!-- ends inc_header.php -->