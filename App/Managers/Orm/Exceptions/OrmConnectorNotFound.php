<?php

namespace Descolar\Managers\Orm\Exceptions;

use Throwable;

class OrmConnectorNotFound extends OrmConnectorException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('OrmConnector class does not exist or is not instantiated', $code, $previous);
    }
}