# Fase 0: política de plataforma, versiones y releases

Estado: decisiones base en revisión  
Fecha: 2026-07-18  
Alcance: Bee Framework y productos derivados

## 1. Propósito

Este documento fija las condiciones que deben existir antes de implementar el nuevo bootstrap, la CLI, las migraciones o el sistema de actualizaciones. Distingue decisiones aprobadas, valores iniciales propuestos y asuntos que todavía requieren validación.

## 2. Decisiones aprobadas

### P0-01. PHP mínimo

Bee Framework y el actualizador requerirán PHP 8.2 o superior. Cada rama mayor deberá declarar también una versión máxima probada; no se asumirá compatibilidad con versiones futuras de PHP sin CI y validación explícita.

### P0-02. Dependencias autocontenidas

Cada release distribuible incluirá `app/vendor/` completo, generado desde un `app/composer.lock` versionado. El servidor cliente:

- no necesita tener Composer instalado;
- no ejecuta `composer install` ni `composer update` durante una actualización;
- no necesita acceso a Packagist, GitHub u otra red para instalar;
- recibe exactamente las versiones de dependencias probadas en CI.

`app/composer.json`, `app/composer.lock` y todos los archivos de `app/vendor/` serán parte administrada y firmada del release. El builder rechazará un release si el lock no corresponde al `composer.json` o si `vendor` no corresponde al lock.

### P0-03. Composer sólo en desarrollo/CI

Composer se utilizará exclusivamente para resolver, auditar y construir dependencias en desarrollo o CI. La generación oficial deberá emplear instalación limpia con opciones equivalentes a producción, sin paquetes de desarrollo y con autoloader optimizado. El comando exacto se fijará al implementar `bee-release-tools`.

No se ejecutarán scripts de Composer de terceros durante el build salvo allowlist expresa y revisada.

### P0-04. Firma obligatoria

Los paquetes se firmarán con Ed25519. `ext-sodium` será requisito obligatorio del runtime que inspecciona o instala actualizaciones. Si no está disponible, el actualizador falla de forma cerrada y explica cómo habilitarla; no degrada a hashes sin firma.

La instalación local analizada usa PHP 8.2.0 y tiene `zip`, PDO y `pdo_mysql`, pero no tiene cargada `sodium`. Esto es un bloqueo conocido para las fases de verificación e instalación, no para continuar el diseño.

### P0-05. Actualización sin red

El flujo manual v1 será completamente offline después de recibir el ZIP. El paquete incluirá código, dependencias, metadatos y migraciones necesarios. La futura distribución central podrá descargar el mismo artefacto, pero no cambiará su verificación local.

### P0-06. Separación de versiones

Se manejarán cuatro identificadores independientes:

| Identificador | Propósito | Regla inicial |
|---|---|---|
| `bee_version` | Versión del framework | SemVer |
| `product_version` | Versión de la aplicación cliente | SemVer por defecto; otro esquema sólo si se declara |
| `package_format_version` | Compatibilidad del ZIP y manifiesto | Entero incremental |
| `database_schema_version` | Estado de migraciones | Historial ordenado, no una constante manual |

La noción heredada de “core version” no será una quinta versión pública. Antes de eliminarla se mapeará explícitamente a `bee_version` para mantener compatibilidad.

### P0-07. Identidad del producto

Cada producto tendrá un `product_id` estable, no secreto, inmutable y distinto del nombre visible. Formato recomendado: dominio invertido, por ejemplo `mx.joystick.mi-producto`. Una actualización destinada a otro `product_id` se rechaza antes de escribir datos.

### P0-08. Releases inmutables

Un release publicado no se reemplaza. Una corrección genera una nueva versión y un nuevo `package_id`. Las ramas Git, nombres de archivo o URLs no se consideran identidad ni fuente de confianza.

## 3. Matriz inicial de compatibilidad

Esta matriz es la base propuesta para la primera implementación; debe validarse en CI antes de declararse soporte oficial.

| Componente | Soporte inicial | Estado |
|---|---|---|
| PHP | 8.2 y 8.3 | Propuesto para CI |
| Sistema operativo | Windows Server/Windows con Apache y Linux x86-64 | Propuesto para CI |
| Servidor web | Apache 2.4 con `mod_rewrite` | Compatible con arquitectura actual |
| Nginx | Configuración equivalente documentada | Pendiente de prueba |
| Base de datos | MySQL 8.0 y MariaDB 10.6+ | Propuesto según el uso actual de PDO MySQL |
| SQLite | No soportado para productos en v1 | PDO está disponible, pero no hay contrato de esquema/backup |
| PostgreSQL | No soportado en v1 | Requeriría adaptador, pruebas y política de migraciones |
| Arquitectura | 64 bits | Propuesto |

“No soportado” significa que el preflight debe rechazar la instalación, no que se intente continuar con advertencias.

## 4. Extensiones PHP

### Obligatorias para el framework base

- `json`;
- `mbstring`;
- `openssl`;
- `pdo`;
- el driver PDO del motor soportado (`pdo_mysql` en v1);
- `session`.

### Obligatorias para el actualizador

- todas las anteriores;
- `fileinfo`;
- `hash`;
- `sodium`;
- `zip`.

### Condicionales según funcionalidades del producto

- `curl`;
- `dom` y `xml`;
- `gd` o `imagick`;
- `intl`.

El manifiesto declarará extensiones adicionales requeridas por cada producto. El builder validará el entorno de construcción y el inspector validará el cliente.

## 5. Clasificación de archivos

Todo producto deberá producir un inventario con una sola clasificación por ruta.

### Administrados

El release puede crear, reemplazar o eliminar estas rutas:

- código de `app/`, excepto configuración persistente y datos runtime;
- `app/composer.json` y `app/composer.lock`;
- `app/vendor/` completo;
- vistas y módulos bajo `templates/`;
- assets estáticos versionados;
- entrypoints y configuración pública controlada, como `index.php` y `.htaccess`.

### Persistentes

Nunca se incluyen, sobrescriben ni eliminan mediante payload ordinario:

- `app/config/.env` y variantes locales;
- `assets/uploads/`;
- `app/logs/`;
- backups, staging, journals y locks;
- archivos generados por usuarios;
- claves privadas y raíz local de confianza.

### Configuración administrada con migración

Archivos como `.htaccess` o plantillas de configuración pueden administrarse, pero cualquier valor específico del cliente debe vivir fuera de ellos o migrarse explícitamente. No se realizará merge textual automático en v1.

### Desarrollo, excluidos del cliente

- `.git/`, `.github/`, `.agents/` y `.codex/`;
- pruebas y fixtures que no sean necesarios para health checks runtime;
- documentación interna de release;
- herramientas de construcción;
- `bee-release-tools`;
- claves de firma;
- configuración local del editor y herramientas como `prepros.config`.

## 6. Política específica de `vendor`

`app/vendor/` se tratará como una unidad generada, no como código editable en instalaciones cliente.

Reglas:

1. Se construye desde cero en un entorno limpio usando el lock versionado.
2. Se excluyen dependencias de desarrollo.
3. Se auditan vulnerabilidades antes de firmar el release.
4. Cada archivo queda cubierto por `checksums.json` y por la firma encadenada.
5. El inventario registra también hash de `composer.json` y `composer.lock`.
6. El preflight rechaza drift dentro de `vendor`; no intenta conservar cambios manuales.
7. La instalación reemplaza el árbol administrado completo y el rollback restaura la versión anterior completa.
8. No se ejecuta Composer ni autoload generado desde el paquete antes de verificar firma y checksums.
9. Licencias y notices exigidos por dependencias se conservan en el artefacto.

Incluir `vendor` aumenta el tamaño de ZIP, staging, backup y tiempo de checksum. El cálculo de espacio deberá contemplar simultáneamente paquete, extracción, versión instalada y backup.

## 7. Canales y ciclo de release

Canales iniciales:

- `stable`: producción;
- `candidate`: validación previa al release;
- `development`: uso interno, nunca ofrecido automáticamente a clientes.

Flujo mínimo:

1. fijar versiones y changelog;
2. resolver dependencias desde lock en entorno limpio;
3. ejecutar análisis, auditoría y pruebas;
4. construir inventario y paquete reproducible;
5. verificar el paquete con un proceso independiente;
6. instalarlo en una instalación limpia y una instalación actualizable de prueba;
7. probar rollback;
8. firmar/publicar el artefacto inmutable;
9. conservar manifiesto, checksums y evidencias del build.

## 8. Compatibilidad y soporte

- Cada release declara origen exacto y destino; v1 no instalará desde cualquier versión anterior.
- Una actualización entre varias versiones requerirá paquetes encadenados o un paquete explícito para ese origen.
- No se permiten downgrades ordinarios; se usa rollback del journal correspondiente.
- Las versiones de PHP, base de datos y extensiones se comprueban antes del backup.
- Un entorno fuera de la matriz puede inspeccionar el motivo del rechazo, pero no forzar instalación desde la UI en v1.

## 9. Decisiones pendientes de fase 0

1. Confirmar PHP 8.3 además de 8.2 como objetivo inicial de CI.
2. Confirmar MySQL 8.0 y MariaDB 10.6+ como únicos motores v1.
3. Definir ruta privada predeterminada para hosting administrado y alternativa para hosting compartido.
4. Definir disponibilidad máxima aceptable durante backup, instalación y rollback.
5. Definir política de soporte y duración de ramas de Bee.
6. Elegir el archivo autoritativo que contendrá `product_id`, `bee_version` y `product_version` sin depender de `.env` para identidad.
7. Decidir si documentación pública del producto se incluye como archivo administrado.

## 10. Criterio de cierre de fase 0

La fase queda cerrada cuando:

- las decisiones pendientes anteriores estén aprobadas;
- exista una matriz CI acordada;
- se elimine `app/composer.lock` de `.gitignore` y se adopte como fuente reproducible (cambio de implementación posterior a aprobación);
- exista una política de inventario aplicable al repositorio y a productos derivados;
- se documente cómo habilitar `ext-sodium` en plataformas soportadas;
- el esquema del manifiesto refleje identidad, versiones, plataforma y dependencias autocontenidas.
