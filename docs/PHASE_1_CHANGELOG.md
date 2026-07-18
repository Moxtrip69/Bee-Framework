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

## Incremento 2 — Bootstrap y configuración compartida

### Añadido

- Raíz canónica `ApplicationRoot`, resuelta desde el directorio del bootstrap y no desde el CWD.
- Modos de ejecución tipados para HTTP, CLI, cron, pruebas y updater.
- Bootstraps separados `common.php`, `http.php`, `cli.php` y `testing.php`.
- Objetos tipados para aplicación, bases de datos, identidad y contexto HTTP.
- Contenedor mínimo de servicios compartido por todos los modos.
- Archivo autoritativo `app/config/identity.php` para identidad y versiones.
- Capa `LegacyConstants` para conservar las constantes globales durante la migración.

### Cambiado

- `index.php` ejecuta primero el bootstrap HTTP y deja `Bee::fly()` como adaptador heredado de despacho.
- `bee_config.php` es ahora un shim de compatibilidad sobre la configuración tipada.
- El updater reutiliza el bootstrap común y conserva su contexto especializado.
- Composer carga componentes nuevos de Core mediante `Bee\\Core\\`.

### Verificación

- CLI carga raíz, configuración y servicios desde un CWD distinto sin sesión ni constantes HTTP.
- HTTP deriva su configuración exclusivamente de un contexto de servidor explícito.
- El bootstrap de pruebas admite overrides sin simular una petición.
- La suite hostil del updater continúa aprobando sus 19 escenarios.

## Incrementos pendientes

- Logging estándar y manejo centralizado de errores.
- Entrada CLI y prueba integral del criterio de salida.
