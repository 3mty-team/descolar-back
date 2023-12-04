<?php


namespace Descolar\Adapters\Router\Exceptions;

use ReflectionMethod;
use Throwable;

class EndPointIsNotPrivateException extends \RuntimeException
{

    public function __construct(ReflectionMethod $endPoint, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("The endpoint {$endPoint->getName()} should be private", $code, $previous);
    }

}