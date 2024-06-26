<?php

namespace Descolar\Adapters\Validator;

use Descolar\Adapters\Validator\Exceptions\ValidateNameIsDuplicatedException;
use Descolar\Adapters\Validator\Parts\PropertyContainer;
use Descolar\Managers\Validator\Annotations\Property;
use Descolar\Managers\Validator\Exceptions\PropertyIsNotValidException;
use Descolar\Managers\Validator\Interfaces\IValidator;
use Error;
use Override;

class ValidatorChecker implements IValidator
{

    /** @var array{string, ValidatorChecker} */
    private static array $instances = [];

    private string $clazzName;

    /**
     * @param object $entity the entity to be checked
     * @param string $clazzName the name of the class
     * @param PropertyContainer[] $containers the properties of the class (annotations)
     */
    public function __construct(
        private object $entity,
        public array   $containers)
    {
        $this->clazzName = get_class($entity);

        self::$instances[$this->clazzName] = $this;

        $this->saveContainers();

        //TODO check if containers are ok (Not same name, etc)...
    }

    public static function buildChecker(object $entity, array $containers): ValidatorChecker
    {
        return new ValidatorChecker($entity, $containers);
    }

    public static function getInstance(object $entity): ValidatorChecker
    {
        $clazzName = get_class($entity);

        if (!isset(self::$instances[$clazzName])) {
            self::$instances[$clazzName] = new ValidatorChecker($entity, []);
        }

        return self::$instances[$clazzName];
    }


    private function saveContainers(): void
    {
        $nameList = [];

        foreach ($this->containers as $container) {
            $name = $container->getName();
            if (in_array($name, $nameList)) {
                throw new ValidateNameIsDuplicatedException($name, $this->clazzName);
            }
            $nameList[] = $name;
        }
    }

    private function checkOne(PropertyContainer $propertyContainer): void
    {
        $propertyCheckName = $this->getPropertyName($propertyContainer->getClazzName());

        $reflectionEntity = new \ReflectionObject($this->entity);
        $propertyReflection = $reflectionEntity->getProperty($propertyCheckName);
        $propertyValue = $this->getPropertyValue($propertyReflection);

        /** @var Property $property */
        foreach ($propertyContainer->getProperties() as $property) {
            $propertyName = get_class($property);

            if (!$property->check($propertyValue)) {
                throw new PropertyIsNotValidException($propertyCheckName, $this->clazzName);
            }
        }
    }

    #[Override] public function check(string ...$ignoreProperties): void
    {
        $propertiesToIgnore = [...$ignoreProperties];
        foreach ($this->containers as $container) {
            if (in_array($container->getName(), $propertiesToIgnore)) {
                continue;
            }

            $this->checkOne($container);
        }
    }

    #[Override] public function checkProperty(string $propertyName): void
    {
        foreach ($this->containers as $container) {
            if ($container->getName() === $propertyName) {
                foreach ($container->getProperties() as $property) {
                    $this->checkOne($property);
                }
            }
        }
    }

    private function getPropertyName(string $getClazzName): false|string
    {
        //get only last part of /
        $parts = explode('\\', $getClazzName);
        return end($parts);
    }

    private function getPropertyValue(\ReflectionProperty $property)
    {
        try {
            return $property->getValue($this->entity);
        } catch (Error $e) {
            return null;
        }
    }
}