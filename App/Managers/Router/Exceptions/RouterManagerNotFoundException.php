<?php

namespace Descolar\Managers\Router\Exceptions;

use Throwable;

class RouterManagerNotFoundException extends RouterException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('RouterManager class does not exist or is not instantiated', $code, $previous);
    }
}