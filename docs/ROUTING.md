# Router moderno de Bee Framework

## Registro básico

Las rutas web se registran en `app/routes/web.php` y las APIs en `app/routes/api.php`. Los archivos contienen declaraciones independientes; no deben retornar una función.

```php
<?php

declare(strict_types=1);

use Bee\Core\Routing\Route;

Route::get('/', [homeController::class, 'index'])
    ->name('home');

Route::get('/users/{id}', [userController::class, 'show'])
    ->whereNumber('id')
    ->name('users.show');

Route::post('/users', [userController::class, 'store'])
    ->name('users.store');

Route::match(['PUT', 'PATCH'], '/users/{id}', [userController::class, 'update'])
    ->whereNumber('id')
    ->name('users.update');

Route::delete('/users/{id}', [userController::class, 'destroy'])
    ->whereNumber('id')
    ->name('users.destroy');
```

Métodos disponibles: `get`, `head`, `post`, `put`, `patch`, `delete`, `options`, `match` y `any`.

## Parámetros

```php
Route::get('/posts/{slug}', [postController::class, 'show'])
    ->where('slug', '[a-z0-9-]+');

Route::get('/archive/{year?}', [archiveController::class, 'index'])
    ->whereNumber('year');
```

También existen `whereAlpha()` y `whereUuid()`. Los parámetros opcionales se admiten al final del path.

El dispatcher resuelve parámetros por nombre y puede convertirlos a `int`, `float`, `bool` o `string` según la firma del método.

```php
public function show(int $id): array
{
    return ['id' => $id];
}
```

## Grupos

```php
Route::prefix('/api/v1')
    ->name('api.')
    ->middleware(['api', 'auth'])
    ->group(static function (): void {
        Route::get('/users/{id}', [apiController::class, 'showUser'])
            ->whereNumber('id')
            ->name('users.show');
    });
```

## Middleware

Los alias se registran en `app/routes/middleware.php`:

```php
$middleware->register('auth', new AuthenticationMiddleware());
```

Un middleware implementa `Bee\Core\Routing\Middleware`:

```php
final class AuthenticationMiddleware implements Middleware
{
    public function process(HttpRequest $request, RouteMatch $match, callable $next): HttpResponse
    {
        if (!$this->isAuthorized($request)) {
            return HttpResponse::json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
```

Los alias `api` y `ajax` existen inicialmente como marcadores compatibles para definir `DOING_API` y `DOING_AJAX`. No autentican por sí mismos.

## Acciones y respuestas

Se admiten controladores, closures y clases invocables:

```php
Route::get('/health', HealthCheckAction::class);

Route::get('/ping', static fn (): string => 'pong');
```

Las acciones pueden devolver:

- `HttpResponse`;
- un arreglo, convertido a JSON;
- un string o escalar;
- `null` cuando la acción escribió directamente la respuesta, conservando controladores heredados.

## Rutas nombradas

```php
$url = route('users.show', ['id' => 42]);
```

Los parámetros adicionales se convierten en query string.

## Comportamiento HTTP

- Una ruta `HEAD` explícita tiene prioridad; de lo contrario se reutiliza `GET` sin emitir body.
- `OPTIONS` se genera automáticamente cuando el path existe.
- Un path moderno solicitado con un verbo incorrecto responde `405` y `Allow`.
- `_method` permite que un formulario `POST` solicite `PUT`, `PATCH` o `DELETE`.

## Compatibilidad heredada

La resolución sigue este orden:

1. Buscar una ruta moderna por path y verbo.
2. Responder `405` si el path moderno existe con otro verbo.
3. Procesar `OPTIONS` automáticamente para paths modernos.
4. Si el path no está registrado, ejecutar el router heredado `/controlador/método/parámetros`.

No se requiere modificar `.htaccess`, controladores existentes ni URLs actuales. La migración puede realizarse ruta por ruta.
