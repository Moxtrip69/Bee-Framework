<?php

declare(strict_types=1);

namespace Bee\Core\Bootstrap;

use Bee\Core\Compatibility\LegacyConstants;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Http\HttpConfig;
use Bee\Core\Http\HttpRequestContext;
use LogicException;

final readonly class HttpBootstrap
{
    public function __construct(private LegacyConstants $legacyConstants = new LegacyConstants())
    {
    }

    /** @param array<string, mixed> $server */
    public function boot(ApplicationContext $application, array $server): ApplicationContext
    {
        if ($application->mode !== ExecutionMode::Http) {
            throw new LogicException('HTTP bootstrap requires HTTP execution mode.');
        }

        $request = HttpRequestContext::fromServer($server);
        $http = new HttpConfig($request, $application->configuration->application);
        $application->services->set(HttpRequestContext::class, $request);
        $application->services->set(HttpConfig::class, $http);
        $this->legacyConstants->defineHttp($http);

        return $application;
    }
}
