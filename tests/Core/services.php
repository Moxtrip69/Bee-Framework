<?php

declare(strict_types=1);

use Bee\Core\Error\CentralErrorHandler;
use Bee\Core\Error\ThrowableHandler;
use Bee\Core\Exception\ServiceNotFoundException;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Logging\FileLogger;
use Bee\Core\Logging\Logger;
use Bee\Core\Logging\LogLevel;

require __DIR__ . '/bootstrap.php';

$applicationRoot = dirname(__DIR__, 2);
$environmentOverrides = ['APP_DEBUG' => 'false'];
$application = require $applicationRoot . '/app/bootstrap/testing.php';

coreAssert($application->services->get(Logger::class) instanceof Logger, 'A standard logger must be registered.');
coreAssert($application->services->get(ThrowableHandler::class) instanceof ThrowableHandler, 'A central error handler must be registered.');
coreAssert($application->services->get(ApplicationRoot::class) === $application->root, 'Core services must share the same root object.');

try {
    $application->services->get('missing.service');
    throw new RuntimeException('Missing services must throw a specific exception.');
} catch (ServiceNotFoundException) {
}

$temporaryLog = tempnam(sys_get_temp_dir(), 'bee-log-');
coreAssert(is_string($temporaryLog), 'A temporary log file must be available.');
$logger = new FileLogger($temporaryLog);
$logger->log(LogLevel::Info, 'redaction test', ['password' => 'private', 'safe' => 'visible']);
$record = file_get_contents($temporaryLog);
unlink($temporaryLog);
coreAssert(is_string($record) && str_contains($record, '[REDACTED]'), 'Sensitive logging context must be redacted.');
coreAssert(!str_contains($record, 'private'), 'Sensitive values must not be written.');

$handler = new CentralErrorHandler($logger = new \Bee\Core\Logging\NullLogger(), $application->mode, false);
try {
    $handler->handleError(E_USER_WARNING, 'converted warning', __FILE__, __LINE__);
    throw new RuntimeException('PHP errors must be converted to exceptions.');
} catch (ErrorException) {
}

echo "PASS: logging and error services are standardized\n";
