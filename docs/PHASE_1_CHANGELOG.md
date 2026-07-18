# Changelog técnico — Fase 1: estabilización del núcleo

Fecha de inicio: 2026-07-18  
Estado: en progreso

Este documento registra los cambios realizados para que HTTP, CLI, cron, pruebas y updater compartan configuración y servicios sin simular una petición web.

## Incremento 1 — Base segura del updater

### Añadido

- Autoload PSR-4 `Bee\\Updater\\` sobre `app/src/Updater/`.
- `app/composer.lock` como fuente reproducible de dependencias.
- Bootstrap del updater con raíz explícita, independiente de `getcwd()`, sesiones y `Bee::fly()`.
- Contratos tipados para manifiesto, checksums, filesystem, reloj y claves públicas.
- Esquemas JSON formales para `manifest.json` y `checksums.json`.
- Decodificador JSON estricto con límites, UTF-8 sin BOM y rechazo de claves duplicadas.
- Validación Ed25519 con Sodium sobre los bytes exactos del manifiesto.
- Inspección ZIP sin extracción y verificación por streaming de todo el payload.
- Límites contra ZIP bombs y controles para Zip Slip, enlaces, archivos especiales, colisiones y rutas persistentes.

### Compatibilidad

- No se modificó el autoloader heredado ni se añadieron namespaces a clases existentes.
- El bootstrap HTTP heredado permanece intacto en este incremento.

### Verificación

- Suite autocontenida del updater con 19 escenarios válidos y hostiles.
- Sintaxis PHP validada para todos los componentes nuevos.
- Esquemas JSON parseados correctamente.
- Autoload optimizado correctamente con Composer.

## Incrementos pendientes

- Raíz autoritativa y bootstraps común, HTTP, CLI y pruebas.
- Configuración tipada y compatibilidad mediante constantes.
- Identidad y versiones centralizadas.
- Logging estándar y manejo centralizado de errores.
- Entrada CLI y prueba integral del criterio de salida.
