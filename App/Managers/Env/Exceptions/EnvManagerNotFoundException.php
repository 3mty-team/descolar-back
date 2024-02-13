<?php

namespace Descolar\Managers\Env\Exceptions;

use Throwable;

class EnvManagerNotFoundException extends EnvException
{

    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($code, $previous);
    }

}