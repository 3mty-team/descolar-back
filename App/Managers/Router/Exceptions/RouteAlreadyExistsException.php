<?php


namespace Descolar\Managers\Router\Exceptions;

use Throwable;

class RouteAlreadyExistsException extends RouterException
{
    public function __construct(string $name, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("Route $name already exists", 500, $previous);
    }

}