<?php

namespace Descolar\Adapters\Router\Exceptions;

use Descolar\Managers\Router\Exceptions\RouterException;
use ReflectionMethod;
use Throwable;

/**
 * Exception thrown when the api is called too many times
 */
class TooManyRequestsException extends RouterException
{

    public function __construct(int $code = 429)
    {
        parent::__construct("Too many requests", $code);
    }

}