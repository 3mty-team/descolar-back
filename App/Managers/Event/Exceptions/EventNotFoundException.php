<?php

namespace Descolar\Managers\Event\Exceptions;

use Throwable;

/**
 * Exception thrown when the swagger class does not exist or is not instantiated
 */
class EventNotFoundException extends EventException
{
    public function __construct(int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct('Event class does not exist or is not instantiated', $code, $previous);
    }
}