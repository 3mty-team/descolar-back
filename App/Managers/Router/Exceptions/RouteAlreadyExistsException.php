<?php


namespace Descolar\Managers\Router\Exceptions;

use Throwable;

/**
 * Exception thrown when the route already exists
 */
class RouteAlreadyExistsException extends RouterException
{
    public function __construct(string $name, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("Route $name already exists", $code, $previous);
    }

}