<?php

declare(strict_types=1);

namespace Bee\Core\Bootstrap;

use Bee\Core\Compatibility\LegacyConstants;
use Bee\Core\Config\OptionRepository;
use Bee\Core\Config\PdoOptionRepository;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Http\HttpConfig;
use Bee\Core\Http\HttpRequestContext;
use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\ResponseEmitter;
use Bee\Core\Routing\MiddlewareRegistry;
use Bee\Core\Routing\PassThroughMiddleware;
use Bee\Core\Routing\Route;
use Bee\Core\Routing\RouteDispatcher;
use Bee\Core\Routing\Router;
use Bee\Core\Routing\UrlGenerator;
use LogicException;

final readonly class HttpBootstrap
{
    public function __construct(private LegacyConstants $legacyConstants = new LegacyConstants())
    {
    }

    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     */
    public function boot(ApplicationContext $application, array $server, array $query = [], array $body = []): ApplicationContext
    {
        if ($application->mode !== ExecutionMode::Http) {
            throw new LogicException('HTTP bootstrap requires HTTP execution mode.');
        }

        $request = HttpRequestContext::fromServer($server);
        $http = new HttpConfig($request, $application->configuration->application);
        $application->services->set(HttpRequestContext::class, $request);
        $application->services->set(HttpConfig::class, $http);
        $database = $request->isLocal()
            ? $application->configuration->developmentDatabase
            : $application->configuration->productionDatabase;
        $application->services->set(OptionRepository::class, new PdoOptionRepository($database));
        $httpRequest = HttpRequest::fromInput($server, $query, $body, $http);
        $router = new Router();
        $middleware = new MiddlewareRegistry();
        $middleware->register('api', new PassThroughMiddleware());
        $middleware->register('ajax', new PassThroughMiddleware());
        $middlewareFile = $application->root->join('app', 'routes', 'middleware.php');
        if (is_file($middlewareFile)) {
            require $middlewareFile;
        }
        $dispatcher = new RouteDispatcher($application->services, $middleware);
        $application->services->set(HttpRequest::class, $httpRequest);
        $application->services->set(Router::class, $router);
        $application->services->set(MiddlewareRegistry::class, $middleware);
        $application->services->set(RouteDispatcher::class, $dispatcher);
        $application->services->set(ResponseEmitter::class, new ResponseEmitter());
        $application->services->set(UrlGenerator::class, new UrlGenerator($router->routes(), $http->baseUrl));
        $this->legacyConstants->defineHttp($http);

        Route::useRouter($router);
        try {
            foreach (['web.php', 'api.php'] as $routeFile) {
                $path = $application->root->join('app', 'routes', $routeFile);
                if (is_file($path)) {
                    require $path;
                }
            }
            $router->finalize();
        } finally {
            Route::clearRouter();
        }

        return $application;
    }
}
