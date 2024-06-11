<?php

namespace Descolar\Managers\Endpoint\Exceptions;

use RuntimeException;

class EndpointException extends RuntimeException
{

    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

}