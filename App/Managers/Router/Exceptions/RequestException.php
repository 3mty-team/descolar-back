<?php

namespace Descolar\Managers\Router\Exceptions;

use Throwable;

/**
 * Exception thrown when the request method does not exist
 */
class RequestException extends RouterException
{
    public function __construct(string $requestMethod, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("$requestMethod does not exist", $code, $previous);
    }
}