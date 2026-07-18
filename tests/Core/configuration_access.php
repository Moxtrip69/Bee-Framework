<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Config\OptionRepository;
use Bee\Core\Container\ServiceContainer;
use Bee\Core\Http\HttpRequest;
use Bee\Core\Routing\MiddlewareRegistry;
use Bee\Core\Routing\RouteDispatcher;
use Bee\Core\Routing\RouteMatch;
use Bee\Core\Routing\Router;

require __DIR__ . '/bootstrap.php';

$applicationRoot = dirname(__DIR__, 2);
$executionMode = 'test';
$environmentOverrides = ['CUSTOM_SETTING' => 'available'];
$application = require $applicationRoot . '/app/bootstrap/common.php';

coreAssert(config('CUSTOM_SETTING') === 'available', 'config() must expose custom environment values.');
coreAssert(config('MISSING_SETTING', 'fallback') === 'fallback', 'config() must support defaults.');
coreAssert(config() === $application->configuration, 'config() without a key must return Configuration.');

$application->services->set(OptionRepository::class, new class implements OptionRepository {
    public function get(string $key, mixed $default = null): mixed
    {
        return $key === 'site.color' ? '#ffcc00' : $default;
    }
});
coreAssert(option('site.color') === '#ffcc00', 'option() must use the registered repository.');
coreAssert(option('missing', 'fallback') === 'fallback', 'option() must support defaults.');

$router = new Router();
$route = $router->get('/configuration', [ConfigurationInjectionController::class, 'show']);
$match = $router->resolve('GET', '/configuration')->match;
coreAssert($match instanceof RouteMatch, 'Configuration injection route must match.');
$response = (new RouteDispatcher($application->services, new MiddlewareRegistry()))
    ->dispatch($match, new HttpRequest(\Bee\Core\Routing\HttpMethod::Get, '/configuration'));
coreAssert($response->body === 'available', 'Configuration must be injectable into controller constructors.');

echo "PASS: configuration helpers and controller injection work\n";

final class ConfigurationInjectionController
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function show(): string
    {
        return (string) $this->configuration->value('CUSTOM_SETTING');
    }
}
