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

  <!-- Meta Pixel Code -->
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '3541935069465214');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=3541935069465214&ev=PageView&noscript=1"
  /></noscript>
  <!-- End Meta Pixel Code -->
</head>

<body>
<!-- ends inc_header.php -->