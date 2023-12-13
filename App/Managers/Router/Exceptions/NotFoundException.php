<?php

namespace Descolar\Managers\Router\Exceptions;

use Throwable;

/**
 * Exception thrown when the page does not exist
 */
class NotFoundException extends RouterException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Page not found', 404, $previous);
    }
}