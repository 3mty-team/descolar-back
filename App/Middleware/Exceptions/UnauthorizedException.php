<?php

namespace Descolar\Middleware\Exceptions;

use Throwable;

/**
 * Exception thrown when the endpoint is not private
 */
class UnauthorizedException extends \RuntimeException
{
    /**
     * @param string $message The message of the exception
     * @param int $code The code of the exception
     * @param Throwable|null $previous The previous exception
     */
    public function __construct(
        string $message = 'Unauthorized',
        int $code = 401,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}