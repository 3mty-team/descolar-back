<?php

namespace Descolar\Adapters\Router\Exceptions;

use Descolar\Managers\Router\Exceptions\RouterException;
use ReflectionMethod;
use Throwable;

/**
 * Exception thrown when the endpoint is not private
 */
class EndPointIsNotPrivateException extends RouterException
{

    public function __construct(ReflectionMethod $endPoint, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("The endpoint {$endPoint->getName()} should be private", $code, $previous);
    }

}