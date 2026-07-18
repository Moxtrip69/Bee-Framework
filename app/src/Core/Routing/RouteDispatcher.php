<?php

declare(strict_types=1);

namespace Bee\Core\Routing;

use Bee\Core\Container\ServiceContainer;
use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\HttpResponse;
use Bee\Core\Routing\Exception\InvalidRouteException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;

final readonly class RouteDispatcher
{
    public function __construct(
        private ServiceContainer $services,
        private MiddlewareRegistry $middleware
    ) {
    }

    public function dispatch(RouteMatch $match, HttpRequest $request): HttpResponse
    {
        $destination = fn (HttpRequest $request): HttpResponse => $this->invoke($match, $request);

        foreach (array_reverse($match->route->middlewareNames()) as $name) {
            $next = $destination;
            $current = $this->middleware->get($name);
            $destination = static fn (HttpRequest $request): HttpResponse => $current->process($request, $match, $next);
        }

        return $destination($request);
    }

    private function invoke(RouteMatch $match, HttpRequest $request): HttpResponse
    {
        [$callable, $reflection] = $this->resolveAction($match->route->action());
        $arguments = $this->resolveArguments($reflection, $match, $request);

        ob_start();
        try {
            $result = $callable(...$arguments);
            $output = (string) ob_get_clean();
        } catch (\Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }

        if ($result instanceof HttpResponse) {
            return $output === '' ? $result : new HttpResponse($output . $result->body, $result->status, $result->headers);
        }
        if (is_array($result)) {
            $response = HttpResponse::json($result);
            return $output === '' ? $response : new HttpResponse($output . $response->body, $response->status, $response->headers);
        }
        if ($result === null) {
            return new HttpResponse($output);
        }
        if (is_scalar($result) || $result instanceof \Stringable) {
            return new HttpResponse($output . (string) $result);
        }

        throw new InvalidRouteException(sprintf('Unsupported route response type: %s.', get_debug_type($result)));
    }

    /** @return array{callable, ReflectionFunctionAbstract} */
    private function resolveAction(mixed $action): array
    {
        if (is_array($action) && count($action) === 2 && (is_string($action[0]) || is_object($action[0])) && is_string($action[1])) {
            $controller = is_object($action[0]) ? $action[0] : $this->resolveObject($action[0]);
            if (!is_callable([$controller, $action[1]])) {
                throw new InvalidRouteException(sprintf('Route controller action is not callable: %s::%s.', get_debug_type($controller), $action[1]));
            }
            return [[$controller, $action[1]], new ReflectionMethod($controller, $action[1])];
        }

        if (is_string($action) && class_exists($action)) {
            $object = $this->resolveObject($action);
            if (!is_callable($object)) {
                throw new InvalidRouteException(sprintf('Route action class is not invokable: %s.', $action));
            }
            return [$object, new ReflectionMethod($object, '__invoke')];
        }

        if ($action instanceof \Closure) {
            return [$action, new ReflectionFunction($action)];
        }

        if (is_object($action) && is_callable($action)) {
            return [$action, new ReflectionMethod($action, '__invoke')];
        }

        if (is_string($action) && is_callable($action)) {
            return [$action, new ReflectionFunction($action)];
        }

        throw new InvalidRouteException('Route action cannot be resolved.');
    }

    private function resolveObject(string $class): object
    {
        return $this->services->has($class) ? $this->services->get($class) : new $class();
    }

    /** @return list<mixed> */
    private function resolveArguments(ReflectionFunctionAbstract $reflection, RouteMatch $match, HttpRequest $request): array
    {
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                if ($type->getName() === HttpRequest::class) {
                    $arguments[] = $request;
                    continue;
                }
                if ($type->getName() === RouteMatch::class) {
                    $arguments[] = $match;
                    continue;
                }
                if ($this->services->has($type->getName())) {
                    $arguments[] = $this->services->get($type->getName());
                    continue;
                }
            }

            if (array_key_exists($parameter->getName(), $match->parameters)) {
                $arguments[] = $this->cast($match->parameters[$parameter->getName()], $type instanceof ReflectionNamedType ? $type : null);
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }
            if ($parameter->allowsNull()) {
                $arguments[] = null;
                continue;
            }

            throw new InvalidRouteException(sprintf('Cannot resolve route action parameter: %s.', $parameter->getName()));
        }

        return $arguments;
    }

    private function cast(string $value, ?ReflectionNamedType $type): mixed
    {
        return match ($type?->getName()) {
            'int' => filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidRouteException('Route parameter is not a valid integer.'),
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidRouteException('Route parameter is not a valid float.'),
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidRouteException('Route parameter is not a valid boolean.'),
            default => $value,
        };
    }
}
