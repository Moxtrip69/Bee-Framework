<?php

declare(strict_types=1);

namespace Bee\Core\Http;

use Bee\Core\Config\ApplicationConfig;

final readonly class HttpConfig
{
    public string $protocol;

    public string $host;

    public string $basePath;

    public string $baseUrl;

    public string $currentUrl;

    public function __construct(public HttpRequestContext $request, ApplicationConfig $application)
    {
        $this->protocol = $request->secure ? 'https' : 'http';
        $port = $request->isLocal() && $application->port !== '' ? ':' . $application->port : '';
        $this->host = ($request->isLocal() ? 'localhost' : $request->host) . $port;
        $this->basePath = $request->isLocal() ? $application->developmentPath : $application->livePath;
        $this->baseUrl = $this->protocol . '://' . $this->host . $this->basePath;
        $this->currentUrl = $this->protocol . '://' . $this->host . $request->requestUri;
    }
}
