<?php

namespace Descolar\Managers\Validator\Interfaces;

use Descolar\Managers\Validator\Exceptions\PropertyIsNotValidException;

interface IValidator
{

    public static function getInstance(object $entity): IValidator;

    /**
     * check if all properties are valid
     * @param string ...$ignoreProperties the properties to be ignored
     * @throws PropertyIsNotValidException if a property is not valid
     */
    public function check(string ...$ignoreProperties): void;

    /**
     * check if a property is valid
     * @param string $propertyName the property to be checked
     * @throws PropertyIsNotValidException if the property is not valid
     */
    public function checkProperty(string $propertyName): void;

}