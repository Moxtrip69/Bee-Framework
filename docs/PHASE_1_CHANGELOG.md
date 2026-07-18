# Changelog técnico — Fase 1: estabilización del núcleo

Fecha de inicio: 2026-07-18
Estado: finalizada

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

## Incremento 3 — Servicios, logging y errores

### Añadido

- Interfaz estándar `Logger`, niveles tipados y servicios `FileLogger`/`NullLogger`.
- Escritura estructurada JSON Lines con bloqueo de archivo y redacción de contexto sensible.
- Interfaz `ThrowableHandler` y manejador central para errores PHP y excepciones no capturadas.
- Excepciones específicas de configuración, logging y resolución de servicios.
- Contrato `ServiceProvider` para registrar servicios sin introducir lógica en controladores o vistas.

### Cambiado

- El bootstrap común registra logging y manejo de errores para todos los modos de ejecución.
- La función global `logger()` delega al servicio estándar como capa temporal de compatibilidad.
- `Bee` deja de transformar excepciones en `bee_die()` y permite que lleguen al manejador central.

### Verificación

- Resolución de servicios compartidos y excepción específica para servicios ausentes.
- Conversión de errores PHP en `ErrorException`.
- Redacción comprobada de contraseñas y otros campos sensibles en logs.
- Suites de Core y updater aprobadas tras la integración.

## Incremento 4 — CLI y cierre de fase

### Añadido

- Entrypoint `bee` independiente del front controller HTTP.
- Contratos de comando y aplicación CLI con excepción específica para comandos desconocidos.
- Comando `core:status` para verificar configuración, servicios, raíz y aislamiento HTTP.
- Bootstrap de cron construido sobre la misma capa segura de CLI.
- Prueba de proceso real que ejecuta la CLI desde otro CWD y valida su salida JSON.
- Documento de arquitectura y checklist `PHASE_1_CORE_STABILIZATION.md`.

### Cambiado

- `Bee` toma su versión desde la identidad autoritativa.
- `get_core_version()` queda como alias de compatibilidad de `BEE_VERSION`.
- `.env.example` deja de declarar una versión duplicada y referencia `identity.php`.

### Verificación

- `php bee core:status` carga configuración y servicios sin sesión, HTTP, controladores ni `Bee::fly()`.
- Cron carga los mismos servicios sin constantes de petición.
- Todos los bootstraps, servicios y componentes nuevos pasan validación de sintaxis.
- Las suites de Core y updater permanecen aprobadas.
- `composer.json` y `composer.lock` son consistentes y Composer valida sin errores de esquema.

### Deuda de dependencias detectada

`composer audit --locked` reportó 19 avisos existentes en tres dependencias: `twig/twig`, `phenx/php-svg-lib` y `verot/class.upload.php`. No se actualizaron dentro de esta fase porque hacerlo cambia librerías runtime y requiere una matriz de regresión específica. Deben corregirse antes de construir o publicar un release de producción.

## Incrementos pendientes

Ninguno dentro del alcance aprobado para fase 1.

## Incremento 5 — Compatibilidad con análisis estático del editor

### Añadido

- Stub `stubs/legacy_constants.php` con declaraciones globales estáticas y tipos inferibles.
- Cobertura para constantes comunes, HTTP, configuración, correo y estado de petición de Bee.
- Prueba que compara el stub con las constantes creadas por la capa de compatibilidad en runtime.

### Motivo

Los valores runtime se construyen desde objetos tipados mediante `LegacyConstants`. PHP los resolvía correctamente, pero los editores no podían descubrir nombres pasados a métodos auxiliares y marcaban como indefinidos los usos heredados. El stub no se incluye ni ejecuta: existe únicamente para indexación del IDE.
