# Changelog técnico — Router moderno

Fecha de inicio: 2026-07-18
Estado: en progreso

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
