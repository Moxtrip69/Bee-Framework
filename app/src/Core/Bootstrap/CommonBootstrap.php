<?php

declare(strict_types=1);

namespace Bee\Core\Bootstrap;

use Bee\Core\Compatibility\LegacyConstants;
use Bee\Core\Config\ConfigurationLoader;
use Bee\Core\Container\ServiceContainer;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Foundation\ExecutionMode;

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

        $context = new ApplicationContext($root, $mode, $configuration, $services);
        $this->legacyConstants->defineCommon($context);
        $GLOBALS['bee.application'] = $context;

        return $context;
    }
}
