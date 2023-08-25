<?php
// Funciones directamente del proyecto en curso

function decideTextColor($backgroundColor)
{
  // Convertir el color de fondo de hexadecimal a RGB
  list($r, $g, $b) = sscanf($backgroundColor, "#%02x%02x%02x");

  // Calcular luminancia relativa del color de fondo
  $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

  // Determinar el contraste mínimo requerido para colores claros y oscuros
  $minContrastForLightColor = 1.5; // Umbral para colores claros
  $minContrastForDarkColor  = 3.0;  // Umbral para colores oscuros

  // Calcular el contraste entre el color de fondo y el color blanco y negro
  $contrastWithWhite = (255 + 0.05) / ($luminance + 0.05);
  $contrastWithBlack = ($luminance + 0.05) / 0.05;

  // Determinar el color de texto basado en los umbrales de contraste
  if ($contrastWithWhite >= $minContrastForLightColor && $contrastWithBlack >= $minContrastForDarkColor) {
    return "#FFFFFF"; // Blanco para colores claros y oscuros
  } elseif ($contrastWithWhite >= $minContrastForLightColor) {
    return "#FFFFFF"; // Blanco para colores claros
  } else {
    return "#000000"; // Negro para colores oscuros
  }
}