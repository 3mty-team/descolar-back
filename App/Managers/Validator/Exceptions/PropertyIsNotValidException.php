<?php

namespace Descolar\Managers\Validator\Exceptions;

class PropertyIsNotValidException extends ValidatorException
{
    public function __construct(string $rule, string $property, string $clazzName)
    {
        parent::__construct("[$rule] The property $property is not valid in the class $clazzName", 400);
    }

}