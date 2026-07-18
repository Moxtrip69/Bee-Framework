# Fase 1 — Estabilización del núcleo del framework

Estado: finalizada
Fecha de cierre: 2026-07-18

## Objetivo

Permitir que HTTP, CLI, cron, pruebas y updater compartan configuración y servicios sin simular peticiones web.

## Checklist de salida

- [x] La raíz se resuelve desde el directorio del entrypoint/bootstrap y nunca desde `getcwd()`.
- [x] Existen bootstraps común, HTTP, CLI, cron y pruebas.
- [x] El bootstrap común no lee `$_SERVER`; sólo la capa HTTP recibe un contexto de servidor.
- [x] Aplicación, bases de datos, identidad y contexto HTTP usan objetos tipados.
- [x] Las constantes globales se conservan mediante una capa de compatibilidad temporal.
- [x] Composer carga componentes nuevos de Core y Updater mediante PSR-4.
- [x] Sólo los componentes nuevos usan namespaces.
- [x] `app/config/identity.php` es la fuente autoritativa de producto, versión de producto, Bee y formato de paquete.
- [x] Servicios y comandos viven fuera de controladores y vistas y se registran mediante un contenedor.
- [x] Existen excepciones específicas y manejo centralizado de errores.
- [x] Existe una interfaz estándar e inyectable de logging.
- [x] Una orden CLI carga configuración y servicios sin `Bee::fly()`, sesión ni despacho HTTP.

## Capas de bootstrap

```text
app/bootstrap/common.php
  ├── app/bootstrap/http.php
  ├── app/bootstrap/cli.php
  │     └── app/bootstrap/cron.php
  ├── app/bootstrap/testing.php
  └── app/bootstrap/updater.php
```

`index.php` usa el bootstrap HTTP antes de entregar el despacho a la clase heredada `Bee`. El entrypoint `bee` utiliza exclusivamente el bootstrap CLI.

## Prueba del criterio de salida

```console
php bee core:status
```

La salida confirma de forma estructurada:

- configuración tipada cargada;
- logger compartido disponible;
- sesión no iniciada;
- contexto HTTP no cargado;
- clase `Bee` no cargada;
- raíz explícita y canónica.

## Compatibilidad temporal

- `bee_config.php` permanece como shim para integraciones heredadas.
- Las constantes existentes se generan desde objetos tipados mediante `LegacyConstants`.
- Los editores descubren esas constantes mediante `stubs/legacy_constants.php`, que nunca se carga en runtime.
- La función global `logger()` delega al nuevo servicio `Logger`.
- `Bee::fly()` permanece únicamente como adaptador HTTP y despacho heredado.
- `get_core_version()` delega a la versión autoritativa de Bee.

## Límites de esta fase

No se migraron masivamente controladores, modelos o vistas heredadas a namespaces. Los componentes nuevos respetan las nuevas fronteras; la migración del legado puede realizarse incrementalmente sin romper compatibilidad.

La auditoría de Composer detectó avisos de seguridad preexistentes en tres dependencias. Su actualización y prueba de regresión es obligatoria antes de un release, pero permanece separada de la estabilización del bootstrap y los servicios realizada aquí.
