<?php

declare(strict_types=1);

namespace Bee\Core\Http;

final class ResponseEmitter
{
    public function emit(HttpResponse $response, bool $suppressBody = false): void
    {
        if (!headers_sent()) {
            http_response_code($response->status);
            foreach ($response->headers as $name => $value) {
                header($name . ': ' . $value, true);
            }
        }

        if (!$suppressBody) {
            echo $response->body;
        }
    }
}
