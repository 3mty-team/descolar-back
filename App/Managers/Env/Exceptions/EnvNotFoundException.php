<?php

namespace Descolar\Managers\Env\Exceptions;

use Throwable;

class EnvNotFoundException extends EnvException
{

    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct("Env class does not exist or is not instantiated", $code, $previous);
    }

}