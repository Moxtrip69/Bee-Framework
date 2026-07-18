<?php

declare(strict_types=1);

namespace Bee\Core\Bootstrap;

use Bee\Core\Compatibility\LegacyConstants;
use Bee\Core\Config\ConfigurationLoader;
use Bee\Core\Container\ServiceContainer;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Foundation\ExecutionMode;
use Bee\Core\Error\CentralErrorHandler;
use Bee\Core\Error\ThrowableHandler;
use Bee\Core\Logging\FileLogger;
use Bee\Core\Logging\Logger;
use Bee\Core\Logging\NullLogger;

final readonly class CommonBootstrap
{
    public function __construct(
        private ConfigurationLoader $configurationLoader = new ConfigurationLoader(),
        private LegacyConstants $legacyConstants = new LegacyConstants()
    ) {
    }

    /** @param array<string, scalar> $environmentOverrides */
    public function boot(
        ApplicationRoot $root,
        ExecutionMode $mode,
        array $environmentOverrides = []
    ): ApplicationContext {
        $configuration = $this->configurationLoader->load($root, $environmentOverrides);
        $services = new ServiceContainer();
        $services->set(ApplicationRoot::class, $root);
        $services->set(\Bee\Core\Config\Configuration::class, $configuration);

        $logger = $mode === ExecutionMode::Test
            ? new NullLogger()
            : new FileLogger($root->join('app', 'logs', $mode->value . '.log'));
        $errorHandler = new CentralErrorHandler($logger, $mode, $configuration->application->debug);
        $services->set(Logger::class, $logger);
        $services->set(ThrowableHandler::class, $errorHandler);
        $errorHandler->register();

        $context = new ApplicationContext($root, $mode, $configuration, $services);
        $this->legacyConstants->defineCommon($context);
        $GLOBALS['bee.application'] = $context;

        return $context;
    }
}
