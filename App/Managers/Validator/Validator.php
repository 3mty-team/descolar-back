<?php

namespace Descolar\Managers\Validator;

use Descolar\App;
use Descolar\Managers\Validator\Interfaces\IValidator;

class Validator
{

    /**
     * get a validator instance
     * @param object $entity the entity to be validated
     * @return IValidator|null the validator instance
     */
    public static function getInstance(object $entity): ?IValidator
    {
       return App::getValidator()->getInstance($entity);
    }

}