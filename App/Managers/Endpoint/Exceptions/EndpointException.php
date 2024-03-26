<?php

namespace Descolar\Managers\Endpoint\Exceptions;

use RuntimeException;
use Throwable;

class EndpointException extends RuntimeException
{

    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

}