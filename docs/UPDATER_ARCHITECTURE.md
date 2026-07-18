# Arquitectura propuesta del sistema de actualizaciones

Estado: propuesta técnica previa a implementación  
Repositorio analizado: Bee Framework 1.6.0, rama `1.6.0`  
Fecha de análisis: 2026-07-18

## 1. Objetivo y alcance inicial

El sistema debe permitir construir, inspeccionar, instalar y revertir paquetes de actualización reutilizables para productos basados en Bee Framework. La primera entrega será manual: un administrador proporcionará un ZIP por panel o por CLI. Un servicio central de distribución queda fuera del alcance inicial.

Se mantienen dos productos separados:

- `bee-release-tools`: herramienta exclusiva de desarrollo para construir y verificar paquetes.
- `bee-updater`: biblioteca instalada en cada producto para inspección, respaldo, instalación y rollback.

Esta fase sólo fija contratos y decisiones. No autoriza todavía cambios en código ejecutable.

## 2. Arquitectura real encontrada

### 2.1 Arranque y resolución de peticiones

- `index.php` requiere directamente `app/classes/Bee.php` y ejecuta `Bee::fly()`.
- `Bee` inicializa sesión, configuración, Composer, el autocargador propio, funciones globales, CSRF, rutas y despacho de controladores.
- El router se basa en `?uri=...` y reglas de `.htaccess`.
- Los controladores, modelos y clases no usan namespaces. `Autoloader` busca por nombre de archivo en `classes`, `controllers`, `models`, `services` y `utils`.
- Composer vive en `app/composer.json`; no declara `autoload` PSR-4 y `app/composer.lock` está ignorado por Git.

### 2.2 Configuración y contexto de ejecución

- `app/config/bee_config.php` usa `getcwd()` para construir `ROOT`.
- La configuración carga variables desde `app/config/.env` mediante `vlucas/phpdotenv`.
- Varias constantes dependen directamente de `$_SERVER`, incluso `REMOTE_ADDR`, `HTTP_HOST` y `REQUEST_URI`.
- La versión del framework está fijada dentro de `Bee` (`1.6.0`); la versión del producto procede de `APP_VERSION`. Existe además una noción de versión de core que espera `app/core/bee_core_version.php`, aunque `app/core` no está presente en este checkout.

Consecuencia: una CLI no puede arrancar de forma segura reutilizando `Bee::fly()` ni cargando sin adaptación toda la configuración HTTP.

### 2.3 Datos, persistencia y operaciones asíncronas

- `Db` expone una conexión PDO singleton, consultas preparadas y primitivas de transacción.
- No existe un runner ni un historial de migraciones.
- No hay una capa de almacenamiento dedicada para backups, staging, locks o estado del actualizador.
- Las peticiones asíncronas pasan por `ajaxController`, `BeeHttp`, CSRF y las convenciones de respuesta JSON del framework.
- La autorización administrativa se aplica desde controladores; no existe todavía un permiso específico para actualizar.

### 2.4 Actualizador heredado

`beeController::upgrade_core()` descarga un ZIP de una rama de GitHub y copia una lista de archivos directamente sobre producción. Debe considerarse legado inseguro y no debe reutilizarse como motor porque:

- no valida firma ni checksums;
- extrae sin validar rutas (Zip Slip);
- no usa staging, lock, backup ni rollback;
- mezcla descarga, validación, instalación, UI y logging en un controlador;
- usa `assets/uploads` para temporales, una ruta servida por web;
- continúa copiando aunque falten archivos y no ofrece atomicidad;
- confía en una rama mutable y en transporte remoto;
- no contempla migraciones ni un inventario verificable.

No se eliminará hasta que el reemplazo tenga paridad funcional y una migración explícita.

## 3. Decisiones técnicas propuestas

### D-01. Núcleo independiente de HTTP

`bee-updater` será una biblioteca con namespaces, tipos y servicios sin dependencias de `Bee::fly()`, sesiones, constantes de ruta ni respuestas HTTP. Panel y CLI serán adaptadores del mismo caso de uso.

Integración prevista:

```text
Panel Bee / CLI
      |
      v
Application services (inspect, install, rollback)
      |
      +-- Package verification
      +-- Filesystem transaction
      +-- Database migration/backup
      +-- Lock and journal
      +-- Health checks
```

No se recomienda colocar lógica del actualizador en `beeController` o `ajaxController`.

### D-02. Bootstrap mínimo y rutas explícitas

La CLI tendrá un punto de entrada propio (`bee`) que reciba explícitamente la raíz del producto y cargue sólo Composer y la configuración mínima necesaria. No usará `getcwd()` como fuente de verdad ni simulará variables `$_SERVER`.

La futura integración deberá añadir autoload PSR-4 al paquete sin migrar de golpe las clases heredadas. Esa decisión conserva compatibilidad con el autocargador actual.

### D-03. Paquetes completos en la primera versión

La versión 1 usará payload completo de archivos administrados. Aun así, el builder comparará inventarios para declarar eliminaciones. No habrá parches binarios ni paquetes incrementales.

El contrato exacto está en [UPDATE_PACKAGE_SPEC.md](UPDATE_PACKAGE_SPEC.md).

### D-04. Firma antes de interpretar contenido activo

La confianza se establece con Ed25519 mediante Sodium. Se verifica primero la firma del manifiesto como bytes exactos; después el hash de `checksums.json`; finalmente cada entrada del payload. Ninguna migración o script se carga antes de completar esas verificaciones.

La clave privada sólo existe en el entorno de releases. El cliente contiene una o más claves públicas identificadas por `key_id`, lo que permite rotación.

### D-05. Sin scripts PHP arbitrarios en v1

Aunque el objetivo inicial mencionaba `scripts/pre-update.php` y `scripts/post-update.php`, la primera versión no los ejecutará. Un script firmado sigue teniendo acceso irrestricto al proceso y vuelve indeterminista el rollback. La extensibilidad inicial se limita a:

- migraciones SQL declarativas y ordenadas;
- operaciones de archivos descritas por el manifiesto;
- health checks registrados previamente en el producto.

Los hooks PHP sólo podrán evaluarse en una versión posterior con interfaz, allowlist, límites y política de reversión explícitos.

### D-06. Transacción durable con journal

La instalación será una máquina de estados persistida en un journal: `received`, `verified`, `preflighted`, `backed_up`, `maintenance`, `migrating_pre`, `installing`, `migrating_post`, `checking`, `committed`, `rolling_back`, `rolled_back` o `failed`.

Cada transición se registra antes y después de la operación. Esto permite diagnosticar y reanudar rollback tras la terminación abrupta de PHP.

### D-07. Staging y datos fuera de rutas públicas

Por defecto, staging, backups, journals y locks deben vivir fuera del document root. Si el hosting no lo permite, se usará una ruta configurable bajo la aplicación con denegación web explícita y una prueba de preflight que falle de forma cerrada si no puede garantizarse la protección.

Nunca se reutilizará `assets/uploads`.

### D-08. Backup y rollback son obligatorios, pero no prometen atomicidad global

Archivos y base de datos no forman una única transacción ACID. La estrategia será:

1. lock exclusivo;
2. verificación y preflight;
3. backup verificable de archivos administrados y base de datos;
4. mantenimiento;
5. migraciones `pre`;
6. instalación desde staging y eliminaciones;
7. migraciones `post`;
8. health checks;
9. commit del journal y salida de mantenimiento.

Ante fallo se restauran primero archivos y después base de datos, manteniendo mantenimiento activo hasta validar la restauración. Las migraciones destructivas requieren migración inversa o restauración completa de base de datos.

### D-09. Lock de doble alcance

Se usará un lock de archivo exclusivo con metadatos de propietario y tiempo. Opcionalmente podrá complementarse con un lock de base de datos. Un timeout no libera por sí solo un lock: la recuperación debe comprobar que el proceso propietario ya no existe o exigir intervención administrativa.

### D-10. La UI es un adaptador privilegiado

La UI seguirá las convenciones Bee (controlador administrativo, `BeeHttp`, CSRF y JSON), pero sólo invocará servicios. Además de autenticación se exigirá un permiso específico de actualización y reautenticación para instalar o revertir. La subida tendrá límites de tamaño y se moverá inmediatamente al almacenamiento privado.

### D-11. Compatibilidad explícita, no inferida

El producto, canal, versión origen, versión destino, versión mínima/máxima compatible de Bee, PHP y extensiones se validan antes del backup. `APP_VERSION` no basta como identidad de producto; cada instalación necesitará un `product_id` estable y no secreto.

### D-12. El builder debe vivir fuera del artefacto cliente

`bee-release-tools` debe ser un paquete/repositorio de desarrollo separado. Puede compartir contratos y verificadores de lectura con `bee-updater`, pero nunca la clave privada ni comandos de construcción en instalaciones cliente.

### D-13. Dependencias de producción incluidas

Cada paquete completo incluirá `app/vendor/`, construido en desarrollo o CI desde un `app/composer.lock` versionado. No se requerirá Composer ni acceso a red en servidores cliente y el actualizador nunca ejecutará Composer en producción. El árbol `vendor` será administrado, firmado y restaurable como el resto del código.

La política completa de plataforma, ownership y releases está en [PHASE_0_RELEASE_POLICY.md](PHASE_0_RELEASE_POLICY.md).

## 4. Límites de responsabilidad propuestos

```text
bee-release-tools
  ConfigLoader
  InventoryBuilder
  ReleaseDiff
  ManifestBuilder
  ChecksumWriter
  ManifestSigner
  ArchiveBuilder
  PackageVerifier

bee-updater
  Contract/
  Package/PackageInspector
  Security/SignatureVerifier
  Security/ArchivePathValidator
  Preflight/PreflightRunner
  Backup/FileBackup + DatabaseBackup
  Migration/MigrationRunner
  Install/FileInstaller
  Recovery/RollbackManager
  State/Journal
  Lock/UpdateLock
  Health/HealthCheckRunner
  Application/InspectUpdate
  Application/InstallUpdate
  Application/RollbackUpdate

Bee adapters
  CLI commands
  Admin controller + AJAX endpoints + views
```

Las interfaces de filesystem, reloj, procesos, base de datos y logger deben ser inyectables para permitir pruebas sin tocar una instalación real.

## 5. Riesgos encontrados

| Prioridad | Riesgo | Evidencia/impacto | Mitigación propuesta |
|---|---|---|---|
| Crítica | Actualizador heredado no autenticado criptográficamente | Copia contenido de una rama remota sobre producción | Deprecar tras entregar el motor firmado; bloquear su uso durante la transición |
| Crítica | Rollback de base de datos incompleto | No existen migraciones ni historial; DDL de MySQL puede hacer commit implícito | Backup probado, migraciones reversibles y política que rechace cambios no recuperables |
| Crítica | Ejecución de scripts empaquetados | Código PHP tendría privilegios de la aplicación | Excluir scripts arbitrarios de v1 |
| Alta | Bootstrap acoplado a HTTP y CWD | CLI puede resolver rutas/entorno equivocados | Bootstrap mínimo con raíz explícita |
| Alta | Temporales bajo web root | `assets/uploads` es público y contiene cargas | Almacenamiento privado y comprobación de inaccesibilidad |
| Alta | Estado de dependencias no reproducible | `composer.lock` está ignorado actualmente | Versionar el lock, construir `vendor` limpio en CI y distribuirlo firmado; no ejecutar Composer en producción |
| Alta | Extensión criptográfica ausente | El PHP 8.2 local no carga `sodium` | Declararla obligatoria y fallar en preflight hasta habilitarla |
| Alta | Ambigüedad de versiones | Conviven versión Bee, producto y core; falta `app/core` | Contrato de identidad/versiones único y validador estricto |
| Alta | Atomicidad limitada en Windows/hosting compartido | Renombres y archivos abiertos pueden impedir swaps | Preflight de capacidades, instalación por archivo con journal y rollback probado |
| Alta | Zip bombs, Zip Slip y enlaces | `ZipArchive::extractTo()` no es una frontera segura | Validación entrada por entrada, límites y rechazo de enlaces/rutas especiales |
| Alta | Clave pública reemplazable por el mismo paquete | Si vive entre archivos administrados se pierde la raíz de confianza | Claves fuera del payload actualizable o rotación firmada por clave vigente |
| Media | Autorización administrativa demasiado amplia | No hay permiso dedicado a updates | Permiso específico, CSRF, reautenticación y auditoría |
| Media | Cache de PHP/opcache | Código actualizado puede no reflejarse inmediatamente | Health check aislado y estrategia documentada de invalidación/reinicio |
| Media | Espacio estimado incorrectamente | ZIP, staging, backup y BD coexisten | Estimar peor caso y reservar margen configurable |
| Media | Diferencias entre instalaciones | Productos derivados modifican archivos del framework | Clasificar archivos administrados/preservados y detectar drift antes de instalar |

## 6. Preguntas que deben cerrarse antes de implementar

1. ¿Dónde puede ubicarse almacenamiento privado en los hostings soportados? Debe existir una ruta por defecto y una alternativa configurable.
2. ¿Qué motores de base de datos son oficialmente compatibles? El DSN actual es configurable, pero el dump y DDL requieren adaptadores por motor.
3. ¿Cuál será el origen autoritativo de `product_id`, versión de producto y versión Bee?
4. ¿Qué disponibilidad se acepta durante backup y rollback de bases grandes?
5. ¿Qué mecanismo reinicia PHP-FPM/Apache u OPcache cuando el hosting lo requiera?

## 7. Criterios de entrada a implementación

No debe empezar la implementación hasta aprobar:

- este diseño y las preguntas abiertas;
- la política de fase 0 en [PHASE_0_RELEASE_POLICY.md](PHASE_0_RELEASE_POLICY.md);
- el contrato de paquete en [UPDATE_PACKAGE_SPEC.md](UPDATE_PACKAGE_SPEC.md);
- el modelo de amenazas en [UPDATE_SECURITY.md](UPDATE_SECURITY.md);
- una matriz de compatibilidad de PHP, extensiones, SO, servidor web y base de datos;
- una política de ownership/preservación de archivos;
- escenarios de rollback y pruebas de fallo por cada transición del journal.

## 8. Secuencia recomendada posterior

1. Extraer contratos y bootstrap mínimo, con pruebas.
2. Implementar builder e inspector sin instalación.
3. Implementar firma, checksums y validación hostil de ZIP.
4. Implementar journal, lock, staging y backup.
5. Implementar instalación de archivos y rollback.
6. Añadir migraciones SQL y health checks.
7. Integrar CLI.
8. Integrar panel administrativo reutilizando los mismos servicios.
9. Retirar de forma controlada `upgrade_core()`.
10. Diseñar, en otra fase, distribución central y rotación remota de metadatos.
