<?php

namespace Descolar\Managers\Swagger\Exceptions;

use Throwable;

/**
 * Exception thrown when the swagger class does not exist or is not instantiated
 */
class SwaggerNotFoundException extends SwaggerException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('Swagger class does not exist or is not instantiated', $code, $previous);
    }
}