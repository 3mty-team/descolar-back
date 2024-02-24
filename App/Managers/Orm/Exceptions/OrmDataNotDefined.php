<?php

namespace Descolar\Managers\Orm\Exceptions;

use Throwable;

class OrmDataNotDefined extends OrmConnectorException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('Information to the database is not defined', $code, $previous);
    }
}