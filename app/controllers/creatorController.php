<?php

declare(strict_types=1);

use Bee\Core\Creator\ComponentScaffolder;
use Bee\Core\Creator\RouteRepository;
use Bee\Core\Routing\HttpMethod;
use Bee\Core\Routing\Router;

final class creatorController extends Controller implements ControllerInterface
{
    public function __construct(
        private readonly ComponentScaffolder $creator,
        private readonly RouteRepository $managedRoutes,
        private readonly Router $router
    ) {
        if (!is_local()) {
            Flasher::error(get_bee_message(0));
            Redirect::to(DEFAULT_CONTROLLER);
        }
        parent::__construct();
    }

    public function index(): void
    {
        register_scripts(
            [JS . 'creator.js?v=' . rawurlencode((string) get_asset_version())],
            'Bee Creator interface'
        );
        $controllers = [];
        foreach (glob(CONTROLLERS . '*Controller.php') ?: [] as $file) {
            $controllers[] = str_replace('Controller.php', '', basename($file));
        }
        sort($controllers, SORT_NATURAL | SORT_FLAG_CASE);
        $managed = [];
        foreach ($this->managedRoutes->all() as $route) {
            $managed[$route['name']] = $route;
        }
        $routes = [];
        foreach ($this->router->routes()->all() as $route) {
            $action = $route->action();
            $actionLabel = is_array($action)
                ? sprintf('%s@%s', is_object($action[0]) ? $action[0]::class : $action[0], $action[1])
                : (is_string($action) ? $action : 'Closure');
            $name = $route->routeName() ?? '';
            $routes[] = [
                'methods' => implode('|', array_map(static fn (HttpMethod $method): string => $method->value, $route->methods())),
                'path' => $route->path(),
                'action' => $actionLabel,
                'name' => $name,
                'middleware' => implode(', ', $route->middlewareNames()),
                'managed' => $managed[$name] ?? null,
            ];
        }

        $this->setTitle('Bee Creator');
        $this->setView('index');
        $this->setData([
            'controllers' => $controllers,
            'routes' => $routes,
            'httpMethods' => array_map(static fn (HttpMethod $method): string => $method->value, HttpMethod::cases()),
        ]);
        $this->render();
    }

    public function post_controller(): void
    {
        $this->runCreatorAction(function (): string {
            $type = (string) ($_POST['type'] ?? 'modern');
            $routePath = trim((string) ($_POST['route_path'] ?? ''));
            $routeName = trim((string) ($_POST['route_name'] ?? ''));
            $routeMethod = (string) ($_POST['route_method'] ?? 'GET');
            if ($type === 'modern' && $routePath !== '' && $routeName !== '') {
                $this->assertRouteAvailable([$routeMethod], '/' . trim($routePath, '/'), $routeName, null);
            }
            $result = $this->creator->createController(
                (string) ($_POST['filename'] ?? ''),
                $type,
                isset($_POST['generate_view']),
                isset($_POST['use_twig']) ? 'twig' : 'bee'
            );
            if ($type === 'modern' && $routePath !== '' && $routeName !== '') {
                $this->managedRoutes->save([
                    'methods' => [$routeMethod],
                    'path' => $routePath,
                    'controller' => $result['class'],
                    'action' => 'index',
                    'name' => $routeName,
                    'middleware' => [],
                    'constraints' => [],
                ]);
            }

            return sprintf('Controlador <b>%s</b> creado como %s.', $result['class'], $type);
        });
    }

    public function post_model(): void
    {
        $this->runCreatorAction(function (): string {
            $fieldInput = $_POST['fields'] ?? [];
            $fieldDefinitions = is_array($fieldInput)
                ? array_map('strval', $fieldInput)
                : (preg_split('/\s*,\s*/', (string) $fieldInput, -1, PREG_SPLIT_NO_EMPTY) ?: []);
            $fields = $this->creator->parseFields($fieldDefinitions);
            $result = $this->creator->createModel(
                (string) ($_POST['filename'] ?? ''),
                trim((string) ($_POST['table'] ?? '')) ?: null,
                $fields,
                isset($_POST['timestamps'])
            );

            return sprintf('Modelo <b>%s</b> creado para la tabla <b>%s</b>.', $result['class'], $result['table']);
        });
    }

    public function post_view(): void
    {
        $this->runCreatorAction(function (): string {
            $result = $this->creator->createView(
                (string) ($_POST['controller'] ?? ''),
                (string) ($_POST['view_name'] ?? ''),
                isset($_POST['use_twig']) ? 'twig' : 'bee'
            );

            return sprintf('Vista %s creada en <b>%s</b>.', $result['engine'], basename($result['path']));
        });
    }

    public function post_route(): void
    {
        $this->runCreatorAction(function (): string {
            $id = trim((string) ($_POST['route_id'] ?? '')) ?: null;
            $methods = array_values((array) ($_POST['methods'] ?? []));
            $path = '/' . trim((string) ($_POST['path'] ?? ''), '/');
            $name = (string) ($_POST['name'] ?? '');
            $this->assertRouteAvailable($methods, $path, $name, $id);
            $middleware = preg_split('/\s*,\s*/', (string) ($_POST['middleware'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $constraints = [];
            $parameter = trim((string) ($_POST['constraint_parameter'] ?? ''));
            $preset = (string) ($_POST['constraint_preset'] ?? '');
            $presets = [
                'numeric' => '\\d+',
                'alpha' => '[A-Za-z]+',
                'alphanumeric' => '[A-Za-z0-9]+',
                'slug' => '[a-z0-9]+(?:-[a-z0-9]+)*',
                'uuid' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}',
            ];
            $expression = $preset === 'custom'
                ? trim((string) ($_POST['constraint_expression'] ?? ''))
                : ($presets[$preset] ?? '');
            if ($parameter !== '' && $expression !== '') {
                $constraints[$parameter] = $expression;
            }
            $route = $this->managedRoutes->save([
                'methods' => $methods,
                'path' => $path,
                'controller' => (string) ($_POST['controller'] ?? ''),
                'action' => (string) ($_POST['action'] ?? ''),
                'name' => $name,
                'middleware' => $middleware,
                'constraints' => $constraints,
            ], $id);

            return sprintf('Ruta nombrada <b>%s</b> guardada.', $route['name']);
        });
    }

    public function delete_route(): void
    {
        $this->runCreatorAction(function (): string {
            $this->managedRoutes->delete((string) ($_POST['route_id'] ?? ''));

            return 'Ruta administrada eliminada.';
        });
    }

    private function runCreatorAction(callable $action): void
    {
        try {
            if (!Csrf::validate($_POST['csrf'] ?? '')) {
                throw new RuntimeException('Acceso no autorizado.');
            }
            Flasher::success($action());
        } catch (Throwable $throwable) {
            Flasher::error(htmlspecialchars($throwable->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }
        Redirect::to('creator');
    }

    /** @param list<string> $methods */
    private function assertRouteAvailable(array $methods, string $path, string $name, ?string $editingId): void
    {
        $editingName = null;
        foreach ($this->managedRoutes->all() as $managed) {
            if ($managed['id'] === $editingId) {
                $editingName = $managed['name'];
                break;
            }
        }
        $methods = array_map('strtoupper', $methods);
        foreach ($this->router->routes()->all() as $route) {
            if ($editingName !== null && $route->routeName() === $editingName) {
                continue;
            }
            if ($name !== '' && $route->routeName() === $name) {
                throw new RuntimeException(sprintf('Ya existe una ruta nombrada %s.', $name));
            }
            if ($route->path() !== $path) {
                continue;
            }
            $registeredMethods = array_map(
                static fn (HttpMethod $method): string => $method->value,
                $route->methods()
            );
            if (array_intersect($methods, $registeredMethods) !== []) {
                throw new RuntimeException(sprintf('La ruta %s ya utiliza uno de esos verbos HTTP.', $path));
            }
        }
    }
}
