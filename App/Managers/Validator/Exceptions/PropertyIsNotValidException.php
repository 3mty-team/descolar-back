<?php

namespace Descolar\Managers\Validator\Exceptions;

class PropertyIsNotValidException extends ValidatorException
{
    public function __construct(string $property, string $clazzName)
    {
        parent::__construct("The property $property is not valid in the class $clazzName");
    }

}