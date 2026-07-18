<?php

declare(strict_types=1);

/**
 * Static declarations for IDEs and language servers.
 *
 * This file is never loaded by the application. Runtime values are provided by
 * Bee\Core\Compatibility\LegacyConstants and, for request-scoped constants,
 * by the legacy Bee dispatcher. Placeholder values exist only for type inference.
 */

const DS = DIRECTORY_SEPARATOR;
const ROOT = '';
const APP = '';
const CLASSES = '';
const CONFIG = '';
const CORE = '';
const CONTROLLERS = '';
const FUNCTIONS = '';
const MODELS = '';
const LOGS = '';
const TEMPLATES = '';
const INCLUDES = '';
const MODULES = '';
const VIEWS = '';
const IMAGES_PATH = '';
const UPLOADS = '';

const BEE_NAME = '';
const BEE_VERSION = '';
const BEE_LOGO = '';
const BEE_DEVS = '';
const BEE_SUPPORT = '';
const BEE_DONATIONS = '';
const BEE_URL = '';
const BEE_CORE_BOOTSTRAPPED = true;

const PRODUCT_ID = '';
const SITE_VERSION = '';
const PACKAGE_FORMAT_VERSION = 1;
const SITE_NAME = '';
const SITE_CHARSET = '';
const SITE_LANG = '';
const SITE_LOGO = '';
const SITE_FAVICON = '';
const SITE_DESC = '';

const PORT = '';
const DEV_PATH = '';
const LIVE_PATH = '';
const IS_LOCAL = false;
const PROTOCOL = '';
const HOST = '';
const REQUEST_URI = '';
const BASEPATH = '';
const URL = '';
const CUR_PAGE = '';
const ASSETS = '';
const CSS = '';
const FAVICON = '';
const FONTS = '';
const IMAGES = '';
const JS = '';
const COMPONENTS = '';
const PLUGINS = '';
const UPLOADED = '';

const API_AUTH = false;
const API_PUBLIC_KEY = '';
const API_PRIVATE_KEY = '';
const AUTH_SALT = '';
const NONCE_SALT = '';
const SANDBOX = false;
const IS_DEMO = false;
const CSS_FRAMEWORK = '';
const JQUERY = false;
const VUEJS = false;
const AXIOS = false;
const SWEETALERT2 = false;
const TOASTR = false;
const WAITME = false;
const LIGHTBOX = false;
const USE_TWIG = false;

const LDB_ENGINE = '';
const LDB_HOST = '';
const LDB_NAME = '';
const LDB_USER = '';
const LDB_PASS = '';
const LDB_CHARSET = '';
const DB_ENGINE = '';
const DB_HOST = '';
const DB_NAME = '';
const DB_USER = '';
const DB_PASS = '';
const DB_CHARSET = '';

const DEFAULT_CONTROLLER = '';
const DEFAULT_ERROR_CONTROLLER = '';
const DEFAULT_METHOD = '';
const BEE_USERS_TABLE = '';
const BEE_COOKIE_ID = '';
const BEE_COOKIE_TOKEN = '';
const BEE_COOKIE_LIFETIME = 0;
const BEE_COOKIE_PATH = '';
const BEE_COOKIE_DOMAIN = '';
const BEE_COOKIE_HTTPS = false;

const PHPMAILER_EXCEPTIONS = false;
const PHPMAILER_SMTP = false;
const PHPMAILER_DEBUG = false;
const PHPMAILER_HOST = '';
const PHPMAILER_AUTH = false;
const PHPMAILER_USERNAME = '';
const PHPMAILER_PASSWORD = '';
const PHPMAILER_SECURITY = '';
const PHPMAILER_PORT = '';
const PHPMAILER_TEMPLATE = '';

// Defined only after Bee starts processing an HTTP request.
const CSRF_TOKEN = '';
const CONTROLLER = '';
const METHOD = '';
const DOING_AJAX = false;
const DOING_API = false;
const DOING_CRON = false;
const DOING_XML = false;
