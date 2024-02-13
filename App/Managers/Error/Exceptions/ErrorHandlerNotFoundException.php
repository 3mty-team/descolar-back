<?php

namespace Descolar\Managers\Error\Exceptions;

use Throwable;

class ErrorHandlerNotFoundException extends ErrorHandlerException
{

    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('Error Handler class does not exist or is not instantiated', $code, $previous);
    }

}