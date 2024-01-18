<?php

namespace Descolar\Managers\JsonBuilder\Exceptions;

use Throwable;

/**
 * Exception when the JsonBuilder class is not found
 */
class JsonBuilderNotFoundException extends JsonBuilderException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('Swagger class does not exist or is not instantiated', $code, $previous);
    }
}