# Especificación preliminar del paquete de actualización

Estado: contrato v1 formalizado; validadores de fase 1 implementados  
Versión del formato: `1`

## 1. Estructura

```text
release.zip
├── manifest.json
├── signature.sig
├── checksums.json
├── files/
└── migrations/
    ├── pre/
    └── post/
```

No se admiten ejecutables arbitrarios en `scripts/` en la versión 1.

## 2. Reglas generales

- Nombres internos en UTF-8, separados por `/` y relativos a la raíz del ZIP.
- Se rechazan rutas absolutas, segmentos `.` o `..`, bytes NUL, unidades Windows, rutas UNC, enlaces simbólicos/duros y nombres duplicados tras normalización.
- El ZIP no puede contener entradas fuera de las declaradas.
- Se imponen límites configurables de entradas, tamaño por archivo, tamaño total descomprimido y ratio de compresión.
- Los JSON usan UTF-8 sin BOM, claves de objeto únicas y números dentro del rango aceptado por PHP. No se firma una reserialización.

## 3. Cadena de confianza

1. Leer `manifest.json` con límites estrictos.
2. Verificar `signature.sig` sobre los bytes exactos de `manifest.json` con la clave pública indicada por `key_id`.
3. Validar el esquema y la compatibilidad del manifiesto.
4. Verificar que SHA-256 de `checksums.json` coincide con `checksums_sha256` del manifiesto.
5. Validar el esquema de checksums y comprobar todas las entradas antes de instalar.

`signature.sig` contiene la firma Ed25519 binaria codificada en Base64 estándar, sin JSON adicional.

## 4. Manifiesto mínimo

Ejemplo informativo:

```json
{
  "format_version": 1,
  "package_id": "018f1f62-1ef6-7d65-b52f-13cb6e65d37a",
  "product_id": "com.example.product",
  "channel": "stable",
  "source_version": "2026.2.0",
  "target_version": "2026.3.0",
  "bee": {
    "min": "1.6.0",
    "max_exclusive": "2.0.0"
  },
  "runtime": {
    "php_min": "8.2.0",
    "extensions": ["json", "pdo", "sodium", "zip"]
  },
  "created_at": "2026-07-18T18:00:00Z",
  "expires_at": null,
  "key_id": "release-2026-01",
  "checksums_sha256": "<64 caracteres hex>",
  "preserve": ["app/config/.env", "assets/uploads/**", "app/logs/**"],
  "delete": ["ruta/obsoleta.php"],
  "migrations": {
    "pre": [],
    "post": [
      {
        "id": "202607180001_add_column",
        "engine": "mysql",
        "path": "migrations/post/202607180001_add_column.sql",
        "rollback_path": "migrations/rollback/202607180001_add_column.sql"
      }
    ]
  },
  "health_checks": ["database", "bootstrap"]
}
```

Todos los campos son obligatorios salvo `expires_at`; las listas pueden estar vacías. El esquema formal está versionado en `app/resources/updater/schema/manifest-v1.schema.json`.

## 5. Checksums

`checksums.json` es un objeto que mapea cada archivo permitido a su SHA-256 hexadecimal en minúsculas:

```json
{
  "files/app/classes/Example.php": "<sha256>",
  "migrations/post/202607180001_add_column.sql": "<sha256>"
}
```

- Debe haber correspondencia uno a uno entre entradas de archivo del ZIP y checksums, excluidos `manifest.json`, `signature.sig` y `checksums.json`.
- Los directorios no llevan checksum.
- Una ruta no puede aparecer simultáneamente como payload, preservada y eliminada.
- El instalador vuelve a verificar el hash desde staging inmediatamente antes de usar cada archivo.

## 6. Ownership y preservación

- `files/` representa rutas administradas por el producto y se proyecta sobre la raíz de instalación.
- `files/app/vendor/`, `files/app/composer.json` y `files/app/composer.lock` forman parte obligatoria del payload completo de producción.
- `.env`, uploads, logs, backups, journals, locks y datos persistentes están prohibidos en payload y eliminaciones.
- `preserve` documenta rutas que el instalador nunca debe sobrescribir o borrar; no sirve para eludir una colisión del paquete.
- `delete` sólo puede apuntar a rutas que pertenecían al inventario administrado de una release anterior.
- Si un archivo administrado local difiere del inventario instalado, el preflight informa drift y falla por defecto. Una política de override deberá ser explícita y auditable.

El validador v1 bloquea explícitamente `.env`, `app/config/.env*`, `assets/uploads/` y `app/logs/`, aun cuando aparezcan firmados y con checksum correcto.

## 7. Migraciones

La versión 1 acepta únicamente archivos SQL declarados individualmente en el manifiesto. Cada entrada incluye `id` único en todo el paquete, `engine` (`mysql` o `mariadb`), `path` bajo la fase correspondiente y `rollback_path`, que puede ser `null`. Toda ruta SQL debe aparecer también en `checksums.json`.

No se infiere orden por el ZIP: el orden es el del manifiesto. Una migración aplicada se registra de forma durable. SQL incompatible con el motor o con la política de rollback se rechaza en preflight.

## 8. Versiones y reinstalación

- Las versiones siguen SemVer cuando el producto ya lo use; productos con calendario deben declarar y validar su esquema sin comparaciones lexicográficas.
- `source_version` debe coincidir exactamente con el inventario instalado, salvo paquetes de recuperación explícitamente diferenciados en una futura versión del formato.
- `target_version` debe ser superior y no puede estar ya instalada.
- `package_id` impide procesar dos veces el mismo paquete.

## 9. Construcción reproducible

El builder ordenará rutas de forma binaria, normalizará timestamps/permisos del ZIP y producirá el mismo manifiesto y payload para las mismas entradas, excepto campos deliberadamente variables como `package_id` y `created_at`. El verificador de desarrollo ejecutará exactamente las reglas del inspector cliente.

Las dependencias se resolverán en desarrollo o CI desde el `composer.lock` versionado. El paquete incluirá `vendor` sin dependencias de desarrollo y el cliente no ejecutará Composer. El builder comprobará la correspondencia entre `composer.json`, `composer.lock`, el autoloader generado y el árbol de dependencias antes de firmar.

## 10. Elementos pendientes de cerrar

- Formato detallado de migraciones y reversas por motor.
- Política de permisos ejecutables en Unix.
- Convención definitiva de versión para productos Bee existentes.
