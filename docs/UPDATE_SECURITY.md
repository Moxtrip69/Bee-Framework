# Modelo de seguridad del sistema de actualizaciones

Estado: propuesta previa a implementación

## 1. Activos protegidos

- integridad y disponibilidad del código instalado;
- confidencialidad de `.env`, credenciales, backups y datos;
- integridad y disponibilidad de la base de datos;
- raíz de confianza (claves públicas y política de rotación);
- historial, journal y evidencias de auditoría;
- clave privada de firma en el entorno de releases.

## 2. Fronteras de confianza

El ZIP, su nombre, metadatos, JSON, rutas, tamaños, compresión, SQL y contenido son hostiles hasta completar firma, esquema y checksums. También se consideran no confiables la petición web, los metadatos del cliente, el estado local alterado y cualquier respuesta futura del servidor central.

HTTPS protege el transporte, pero no sustituye la firma. SHA-256 detecta cambios, pero sólo una firma vinculada a una clave confiable autentica el origen.

## 3. Amenazas y controles

| Amenaza | Control requerido |
|---|---|
| Paquete falsificado o modificado | Firma Ed25519 del manifiesto y verificación encadenada de checksums |
| Downgrade o paquete para otro producto | Validación exacta de producto, canal, origen, destino y compatibilidad |
| Replay | `package_id`, historial durable y rechazo de versión ya instalada |
| Clave comprometida | `key_id`, revocación local y rotación firmada por clave vigente; nunca actualizar la raíz desde el mismo payload |
| Zip Slip/rutas equivalentes | Normalización independiente de SO y validación de destino bajo raíz permitida |
| Zip bomb/agotamiento | Límites antes y durante extracción, espacio reservado y streaming cuando sea posible |
| Enlaces y archivos especiales | Rechazo de symlinks, hardlinks, dispositivos y atributos no soportados |
| TOCTOU tras validación | Staging privado, permisos restrictivos y rehash antes de instalar |
| Ejecución remota vía hooks | No admitir scripts PHP arbitrarios en v1 |
| SQL destructivo no recuperable | Allowlist/política de migración, backup verificado y reversa declarada |
| Acceso web no autorizado | Autenticación, permiso dedicado, CSRF, reautenticación y rate limiting |
| Dos instalaciones simultáneas | Lock exclusivo con ownership y journal |
| Fuga por logs | Redacción de secretos, rutas sensibles y SQL con datos; auditoría separada |
| Backup accesible públicamente | Almacenamiento fuera del document root y prueba negativa de acceso |
| Corte de energía/proceso | Journal durable con escrituras atómicas y rollback reanudable |
| Dependencia manipulada o build no reproducible | `composer.lock` versionado, `vendor` construido limpio, auditado, firmado y cubierto por checksums |

## 4. Reglas de extracción

Para cada entrada se debe:

1. rechazar nombres vacíos, NUL, rutas absolutas, UNC, letras de unidad y segmentos `.`/`..`;
2. convertir separadores a `/` para validar, sin confiar en el SO anfitrión;
3. detectar duplicados y colisiones sin distinción de mayúsculas cuando el destino pueda ser case-insensitive;
4. resolver el destino y demostrar que permanece bajo staging;
5. rechazar enlaces y tipos especiales usando atributos externos del ZIP;
6. aplicar límites de tamaño y ratio antes de escribir;
7. crear el archivo nuevo sin seguir enlaces preexistentes;
8. calcular SHA-256 durante/después de escritura y compararlo;
9. no ejecutar, incluir ni deserializar PHP procedente del paquete durante inspección.

`ZipArchive::extractTo()` sin validación por entrada no cumple estas reglas.

## 5. Gestión de claves

- La clave privada se genera y almacena fuera del repositorio y fuera del builder distribuible.
- La firma se realiza en un entorno de release con acceso mínimo y auditoría.
- Los clientes reciben claves públicas por un canal confiable durante instalación.
- Las claves públicas no son archivos administrados por una actualización ordinaria.
- La rotación agrega una nueva clave mediante metadatos firmados por una clave aún válida.
- La revocación y respuesta a compromiso deben documentarse antes de habilitar distribución central.
- Los secretos nunca se incluyen en logs, manifiestos, diagnósticos o paquetes.

`ext-sodium` es obligatoria. Si no está disponible, la inspección criptográfica y la instalación se rechazan; no se permite degradar a una validación basada sólo en SHA-256, HTTPS u OpenSSL con otra política improvisada.

## 5.1 Dependencias distribuidas

- `vendor` se considera entrada hostil hasta verificar la firma y todos sus checksums.
- No se carga `app/vendor/autoload.php` desde staging durante la inspección.
- Composer sólo se ejecuta en desarrollo o CI y nunca con credenciales incluidas en el artefacto.
- El build debe auditar dependencias y conservar las licencias requeridas.
- Un cambio local dentro de `vendor` se trata como drift y bloquea la actualización por defecto.

## 6. Seguridad de la interfaz web

- Sólo usuarios autenticados con permiso dedicado pueden inspeccionar paquetes.
- Instalar y revertir requieren CSRF válido y reautenticación reciente.
- La autorización se comprueba en servidor para cada transición, no sólo al mostrar botones.
- Nombre, MIME y extensión informados por el navegador no son confiables.
- Se limita tamaño de request y archivo; el contenido se mueve a almacenamiento privado.
- Las respuestas no exponen trazas, rutas internas, comandos, credenciales ni fragmentos SQL sensibles.
- La operación larga debe usar un worker/CLI o un mecanismo controlado; desconectar el navegador no cancela ni deja ambiguo el journal.

## 7. Backup, rollback y mantenimiento

- El backup se completa y verifica antes de modificar producción.
- Debe incluir archivos que serán sobrescritos/eliminados, inventario, permisos relevantes y base de datos.
- Los backups se cifran cuando el riesgo/hosting lo requiera; sus claves no se almacenan junto al backup.
- Modo mantenimiento se activa antes de la primera mutación y permanece activo si el rollback no concluye.
- Una restauración se considera exitosa sólo después de health checks.
- Se define retención y borrado seguro de backups; no se eliminan automáticamente evidencias del último fallo.

## 8. Registro y auditoría

Registrar como mínimo:

- identificador de operación y paquete;
- usuario/origen CLI;
- hashes, clave usada y resultado de validación;
- versiones origen/destino;
- transiciones del journal con timestamps;
- migraciones ejecutadas;
- resultado de backup, instalación, health checks y rollback.

No registrar contenido de `.env`, credenciales de BD, tokens, cookies, dumps, cuerpos completos del paquete ni datos personales innecesarios.

## 9. Casos de prueba de seguridad mínimos

- firma incorrecta, clave desconocida, manifiesto alterado y checksum incorrecto;
- JSON duplicado, inválido, enorme o con codificación inesperada;
- `../`, rutas absolutas, UNC, unidades Windows, separadores mixtos y NUL;
- colisiones por mayúsculas, Unicode y nombres reservados de Windows;
- symlink/hardlink y archivo especial;
- ZIP bomb, demasiadas entradas y disco lleno;
- drift local, permisos insuficientes y staging expuesto por web;
- lock concurrente, lock huérfano y proceso terminado en cada estado;
- fallo de cada migración, copia, eliminación, health check y restauración;
- intento de downgrade, replay, producto/canal incorrecto y paquete expirado;
- acceso sin sesión, sin permiso, sin CSRF y con sesión expirada.

## 10. Riesgos residuales que requieren aceptación

- No existe atomicidad real entre filesystem y base de datos.
- Un proceso PHP comprometido con permisos de escritura puede alterar código y claves locales; el actualizador reduce el riesgo de suministro, no sustituye la seguridad del host.
- Algunos hostings no permiten operaciones atómicas, workers o almacenamiento fuera del document root.
- DDL y dumps varían por motor y versión; el soporte debe declararse y probarse explícitamente.
- La invalidez de OPcache o archivos abiertos puede requerir reinicio fuera del alcance del proceso web.
