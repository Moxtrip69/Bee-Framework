# Fase 1: contratos y bootstrap mínimo

Estado: finalizada  
Fecha: 2026-07-18

## Alcance entregado

- Namespace `Bee\\Updater\\` aislado del autoloader heredado.
- Raíz de instalación explícita y canónica, sin depender de `getcwd()`.
- Contexto mínimo con información del runtime.
- Contratos iniciales inyectables para reloj y filesystem.
- Bootstrap que carga únicamente Composer; no arranca sesiones, rutas, configuración HTTP ni `Bee::fly()`.
- Pruebas autocontenidas del bootstrap, raíz y extensiones obligatorias inmediatas.
- Esquemas JSON Schema versionados para manifiesto y checksums v1.
- Decodificación JSON estricta con rechazo de BOM, tamaño excesivo, raíz no-objeto y claves duplicadas.
- Validadores de contrato para manifiesto, migraciones, rutas y checksums.
- Proveedor inyectable de claves públicas Ed25519 y verificación con Sodium.
- Firma verificada sobre los bytes exactos del manifiesto y selección local mediante `key_id`.
- Vinculación criptográfica del manifiesto con los bytes exactos de `checksums.json`.
- Inspección ZIP sin extracción, con límites de entradas, tamaños y ratio de compresión.
- Rechazo de Zip Slip, colisiones por mayúsculas, enlaces, archivos especiales, rutas persistentes y contenido no declarado.
- Correspondencia uno a uno entre inventario ZIP y checksums, con hash por streaming de cada payload.
- Comprobación del payload completo de Composer y de todas las migraciones declaradas.

## Uso interno

```php
$applicationRoot = 'C:/ruta/al/producto';
$context = require $applicationRoot . '/app/bootstrap/updater.php';
```

El llamador debe proporcionar siempre `$applicationRoot`. La CLI futura resolverá su opción `--root` y usará este mismo bootstrap.

## Verificación

```console
php tests/Updater/run.php
```

## Criterios de cierre

- El código del actualizador no inicia Bee, sesiones ni configuración HTTP.
- La raíz del producto se recibe explícitamente.
- Los contratos v1 son cerrados y están versionados.
- Un paquete no obtiene confianza antes de validar inventario, firma y checksums.
- Ningún contenido del ZIP se extrae ni ejecuta durante la inspección.
- Los casos válidos y hostiles cubiertos por fase 1 pasan en PHP 8.2 con Sodium.

## Siguiente fase

Implementar `bee-release-tools`: inventario de release, construcción reproducible, generación de checksums, firma y verificación independiente. La instalación, staging, backup, journal y rollback permanecen fuera de fase 1.
