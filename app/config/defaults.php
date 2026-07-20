<?php

declare(strict_types=1);

/**
 * Versioned defaults for framework behavior.
 *
 * Values may be overridden in app/config/.env when an installation needs to
 * differ. Secrets and deployment credentials never belong in this file.
 */
return [
    'APP_DEBUG' => false,
    'DEFAULT_CONTROLLER' => 'bee',
    'DEFAULT_ERROR_CONTROLLER' => 'error',
    'DEFAULT_METHOD' => 'index',
    'APP_DEV_PATH' => '/',
    'APP_LIVE_PATH' => '/',
    'APP_PORT' => '',
    'IS_SANDBOX' => false,
    'IS_DEMO' => false,
    'API_PROTECTED' => true,
    'APP_TIMEZONE' => 'UTC',
    'APP_LANG' => 'es_MX',
    'APP_CHARSET' => 'UTF-8',
    'APP_NAME' => 'Bee application',
    'APP_LOGO' => 'logo.png',
    'APP_FAVICON' => 'favicon.ico',
    'APP_DESC' => '',
    'BEE_USERS_TABLE' => 'bee_users',
    'COOKIE_ID_NAME' => 'bee__cookie_id',
    'COOKIE_TOKEN_NAME' => 'bee__cookie_tkn',
    'COOKIE_LIFETIME' => 'month',
    'COOKIE_PATH' => '/',
    'COOKIE_DOMAIN' => '',
    'COOKIE_HTTPS' => false,
    'PHPMAILER_EXCEPTIONS' => true,
    'PHPMAILER_SMTP' => false,
    'PHPMAILER_DEBUG' => false,
    'PHPMAILER_AUTH' => true,
    'PHPMAILER_SECURITY' => 'tls',
    'PHPMAILER_PORT' => '465',
    'PHPMAILER_TEMPLATE' => 'emailTemplate',
    'CSS_FRAMEWORK' => 'bs5',
    'USE_JQUERY' => true,
    'USE_VUEJS' => true,
    'USE_AXIOS' => false,
    'USE_SWEETALERT2' => true,
    'USE_TOASTR' => true,
    'USE_WAITME' => true,
    'USE_LIGHTBOX' => true,
    'USE_TWIG' => false,
    'LDB_ENGINE' => 'mysql',
    'LDB_CHARSET' => 'utf8mb4',
    'DB_ENGINE' => 'mysql',
    'DB_CHARSET' => 'utf8mb4',
];
