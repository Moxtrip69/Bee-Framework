<?php

declare(strict_types=1);

namespace Bee\Core\Compatibility;

use Bee\Core\Config\Configuration;
use Bee\Core\Foundation\ApplicationContext;
use Bee\Core\Http\HttpConfig;

final class LegacyConstants
{
    public function defineCommon(ApplicationContext $context): void
    {
        $root = $context->root;
        $config = $context->configuration;
        $app = $config->application;
        $identity = $config->identity;
        $separator = DIRECTORY_SEPARATOR;
        $rootPath = $root->path() . $separator;
        $appPath = $rootPath . 'app' . $separator;

        $this->defineMany([
            'DS' => $separator,
            'ROOT' => $rootPath,
            'APP' => $appPath,
            'CLASSES' => $appPath . 'classes' . $separator,
            'CONFIG' => $appPath . 'config' . $separator,
            'CORE' => $appPath . 'core' . $separator,
            'CONTROLLERS' => $appPath . 'controllers' . $separator,
            'FUNCTIONS' => $appPath . 'functions' . $separator,
            'MODELS' => $appPath . 'models' . $separator,
            'LOGS' => $appPath . 'logs' . $separator,
            'TEMPLATES' => $rootPath . 'templates' . $separator,
            'INCLUDES' => $rootPath . 'templates' . $separator . 'includes' . $separator,
            'MODULES' => $rootPath . 'templates' . $separator . 'modules' . $separator,
            'VIEWS' => $rootPath . 'templates' . $separator . 'views' . $separator,
            'IMAGES_PATH' => $rootPath . 'assets' . $separator . 'images' . $separator,
            'UPLOADS' => $rootPath . 'assets' . $separator . 'uploads' . $separator,
            'BEE_NAME' => 'Bee Framework',
            'BEE_VERSION' => $identity->beeVersion,
            'BEE_LOGO' => 'bee_logo.png',
            'BEE_DEVS' => 'J. Roberto Orozco Aviles',
            'BEE_SUPPORT' => 'soporte@joystick.com.mx',
            'BEE_DONATIONS' => 'https://buymeacoffee.com/joystickmx',
            'BEE_URL' => 'https://github.com/Moxtrip69/Bee-Framework',
            'PRODUCT_ID' => $identity->productId,
            'SITE_VERSION' => $identity->productVersion,
            'PACKAGE_FORMAT_VERSION' => $identity->packageFormatVersion,
            'SITE_NAME' => $app->name,
            'SITE_CHARSET' => $app->charset,
            'SITE_LANG' => $app->language,
            'PORT' => $app->port,
            'DEV_PATH' => $app->developmentPath,
            'LIVE_PATH' => $app->livePath,
        ]);

        $this->defineEnvironmentConstants($config);
        date_default_timezone_set($app->timezone);
        $this->define('BEE_CORE_BOOTSTRAPPED', true);
    }

    public function defineHttp(HttpConfig $http): void
    {
        $assets = $http->baseUrl . 'assets/';
        $this->defineMany([
            'IS_LOCAL' => $http->request->isLocal(),
            'PROTOCOL' => $http->protocol,
            'HOST' => $http->host,
            'REQUEST_URI' => $http->request->requestUri,
            'BASEPATH' => $http->basePath,
            'URL' => $http->baseUrl,
            'CUR_PAGE' => $http->currentUrl,
            'ASSETS' => $assets,
            'CSS' => $assets . 'css/',
            'FAVICON' => $assets . 'favicon/',
            'FONTS' => $assets . 'fonts/',
            'IMAGES' => $assets . 'images/',
            'JS' => $assets . 'js/',
            'COMPONENTS' => $assets . 'js/components/',
            'PLUGINS' => $assets . 'plugins/',
            'UPLOADED' => $assets . 'uploads/',
        ]);
    }

    private function defineEnvironmentConstants(Configuration $config): void
    {
        $booleanKeys = [
            'API_AUTH' => 'API_PROTECTED', 'SANDBOX' => 'IS_SANDBOX', 'IS_DEMO' => 'IS_DEMO',
            'JQUERY' => 'USE_JQUERY', 'VUEJS' => 'USE_VUEJS', 'AXIOS' => 'USE_AXIOS',
            'SWEETALERT2' => 'USE_SWEETALERT2', 'TOASTR' => 'USE_TOASTR', 'WAITME' => 'USE_WAITME',
            'LIGHTBOX' => 'USE_LIGHTBOX', 'USE_TWIG' => 'USE_TWIG',
            'BEE_COOKIE_HTTPS' => 'COOKIE_HTTPS', 'PHPMAILER_EXCEPTIONS' => 'PHPMAILER_EXCEPTIONS',
            'PHPMAILER_SMTP' => 'PHPMAILER_SMTP', 'PHPMAILER_DEBUG' => 'PHPMAILER_DEBUG',
            'PHPMAILER_AUTH' => 'PHPMAILER_AUTH',
        ];
        foreach ($booleanKeys as $constant => $environmentKey) {
            $this->define($constant, filter_var($config->value($environmentKey, 'false'), FILTER_VALIDATE_BOOLEAN));
        }

        $stringKeys = [
            'API_PUBLIC_KEY' => 'API_PUBLIC_KEY', 'API_PRIVATE_KEY' => 'API_PRIVATE_KEY',
            'AUTH_SALT' => 'AUTH_SALT', 'NONCE_SALT' => 'NONCE_SALT', 'CSS_FRAMEWORK' => 'CSS_FRAMEWORK',
            'SITE_LOGO' => 'APP_LOGO', 'SITE_FAVICON' => 'APP_FAVICON', 'SITE_DESC' => 'APP_DESC',
            'LDB_ENGINE' => 'LDB_ENGINE', 'LDB_HOST' => 'LDB_HOST', 'LDB_NAME' => 'LDB_NAME',
            'LDB_USER' => 'LDB_USER', 'LDB_PASS' => 'LDB_PASS', 'LDB_CHARSET' => 'LDB_CHARSET',
            'DB_ENGINE' => 'DB_ENGINE', 'DB_HOST' => 'DB_HOST', 'DB_NAME' => 'DB_NAME',
            'DB_USER' => 'DB_USER', 'DB_PASS' => 'DB_PASS', 'DB_CHARSET' => 'DB_CHARSET',
            'DEFAULT_CONTROLLER' => 'DEFAULT_CONTROLLER', 'DEFAULT_ERROR_CONTROLLER' => 'DEFAULT_ERROR_CONTROLLER',
            'DEFAULT_METHOD' => 'DEFAULT_METHOD', 'BEE_USERS_TABLE' => 'BEE_USERS_TABLE',
            'BEE_COOKIE_ID' => 'COOKIE_ID_NAME', 'BEE_COOKIE_TOKEN' => 'COOKIE_TOKEN_NAME',
            'BEE_COOKIE_PATH' => 'COOKIE_PATH', 'BEE_COOKIE_DOMAIN' => 'COOKIE_DOMAIN',
            'PHPMAILER_HOST' => 'PHPMAILER_HOST', 'PHPMAILER_USERNAME' => 'PHPMAILER_USERNAME',
            'PHPMAILER_PASSWORD' => 'PHPMAILER_PASSWORD', 'PHPMAILER_SECURITY' => 'PHPMAILER_SECURITY',
            'PHPMAILER_PORT' => 'PHPMAILER_PORT', 'PHPMAILER_TEMPLATE' => 'PHPMAILER_TEMPLATE',
        ];
        foreach ($stringKeys as $constant => $environmentKey) {
            $this->define($constant, $config->value($environmentKey, ''));
        }

        $periods = ['hour' => 3600, 'day' => 86400, 'week' => 604800, 'month' => 2592000, 'year' => 31536000];
        $this->define('BEE_COOKIE_LIFETIME', $periods[$config->value('COOKIE_LIFETIME', 'week')] ?? 604800);
    }

    /** @param array<string, mixed> $constants */
    private function defineMany(array $constants): void
    {
        foreach ($constants as $name => $value) {
            $this->define($name, $value);
        }
    }

    private function define(string $name, mixed $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}
