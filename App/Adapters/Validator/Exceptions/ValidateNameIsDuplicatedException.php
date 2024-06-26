<?php

namespace Descolar\Adapters\Validator\Exceptions;

use Descolar\Managers\Validator\Exceptions\ValidatorException;
use RuntimeException;

class ValidateNameIsDuplicatedException extends ValidatorException
{
    public function __construct(string $name, string $clazz)
    {
        parent::__construct("Property $name is duplicated in class $clazz");
    }
}