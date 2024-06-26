<?php

namespace Descolar\Adapters\Validator\Exceptions;

use Descolar\Managers\Validator\Exceptions\ValidatorException;
use RuntimeException;

class ValidateAttributeDontExistsException extends ValidatorException
{
    public function __construct(string $name, string $clazz)
    {
        parent::__construct("Attribute $name does not exists in class $clazz");
    }
}