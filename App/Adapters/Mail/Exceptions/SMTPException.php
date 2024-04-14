<?php

namespace Descolar\Adapters\Mail\Exceptions;

use Descolar\Managers\Mail\Exceptions\MailException;

class SMTPException extends MailException
{
    public function __construct(string $message = 'SMTP error', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}