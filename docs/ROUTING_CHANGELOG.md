# Changelog técnico — Router moderno

Fecha de inicio: 2026-07-18
Estado: finalizado

## Incremento 1 — Motor de resolución

### Añadido

- Enum tipado para `GET`, `HEAD`, `POST`, `PUT`, `PATCH`, `DELETE` y `OPTIONS`.
- Definiciones y colección de rutas con detección de duplicados.
- Matching de rutas estáticas, parámetros requeridos y parámetros finales opcionales.
- Restricciones por expresión regular mediante `where()`.
- Precedencia de segmentos estáticos sobre rutas parametrizadas.
- Extracción segura de parámetros y rechazo de separadores codificados.
- Estados explícitos para coincidencia, fallback, `405 Method Not Allowed` y `OPTIONS` automático.
- Fallback implícito de `HEAD` hacia `GET`.
- Grupos internos con prefijo, nombre y middleware.

### Compatibilidad

Un path sin registro devuelve estado `NotFound`; esta señal será utilizada para delegar al router heredado. Un path registrado con verbo incorrecto nunca podrá caer al fallback.

### Verificación

- Verbos y cabecera lógica `Allow`.
- Parámetros y restricciones.
- Precedencia estática.
- Parámetros opcionales.
- Grupos y middleware.
- Duplicados y rutas desconocidas.

## Incremento 2 — API declarativa estilo Laravel

### Añadido

- Fachada `Route` con métodos por verbo, `match()` y `any()`.
- Registro directo en archivos PHP sin `return` ni función contenedora.
- Grupos encadenables con `prefix()`, `name()` y `middleware()`.
- Limpieza explícita de la fachada para evitar estado residual en workers y pruebas.
- Generador de URLs para rutas nombradas, parámetros opcionales y query strings.

### Diseño

La fachada sólo delega al objeto `Router` del contenedor. El motor, colección y generación de URLs siguen siendo objetos inyectables y comprobables.

## Incremento 3 — Request, middleware y despacho

### Añadido

- Request HTTP tipado con método, path relativo al base path, query, body y headers.
- Override seguro `_method` para formularios `POST` hacia `PUT`, `PATCH` o `DELETE`.
- Respuestas HTTP tipadas, JSON, `204` y emisor con soporte para `HEAD`.
- Registro de middleware por alias y pipeline por ruta/grupo.
- Dispatcher para closures, funciones, controladores y clases invocables.
- Inyección de request, match, servicios y parámetros nombrados con conversión escalar.
- Excepciones HTTP con status y headers integradas al manejador central.

## Incremento 4 — Integración con Bee y fallback heredado

### Añadido

- Registro de Router, request, middleware, dispatcher, emitter y generador de URL en el bootstrap HTTP.
- Archivos declarativos `app/routes/web.php` y `app/routes/api.php`.
- Alias iniciales `api` y `ajax` para clasificación compatible de peticiones.
- Resolución moderna dentro de Bee antes del análisis controlador/método heredado.
- Respuesta automática `OPTIONS` y `405` sin permitir bypass hacia el router anterior.

### Compatibilidad

- `NotFound` en el router moderno conserva el despacho `/controlador/método/parámetros` sin cambios.
- Se mantienen hooks, sesión, autenticación, CSRF, globales y constantes del ciclo Bee.
- `CONTROLLER`, `METHOD`, `DOING_API` y `DOING_AJAX` reciben valores compatibles al usar rutas modernas.

### Verificación

- Una ruta moderna registrada se procesa mediante el dispatcher nuevo.
- Una URL no registrada conserva el controlador, método y parámetros heredados.

## Incremento 5 — Ergonomía y documentación

### Añadido

- Ruta `HEAD` explícita con prioridad sobre el fallback `GET`.
- Restricciones `whereNumber()`, `whereAlpha()` y `whereUuid()`.
- Archivo independiente para registrar alias de middleware.
- Helper global `route()` para generación de URLs nombradas en vistas heredadas.
- Guía completa de registro, grupos, middleware, respuestas y migración compatible.
