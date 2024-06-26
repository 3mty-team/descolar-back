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

        foreach ($this->containers as $containerList) {
            foreach ($containerList as $container) {
                $name = $container->getName();
                if (in_array($name, $nameList)) {
                    throw new ValidateNameIsDuplicatedException($name, $this->clazzName);
                }
                $nameList[] = $name;
            }
        }
    }

    private function checkOne(PropertyContainer $container, Property $property): void
    {
        $containerName = $this->getPropertyName($container->getName());
        $propertyName = $this->getPropertyName(get_class($property));

        $reflectionEntity = new \ReflectionObject($this->entity);
        $propertyReflection = $reflectionEntity->getProperty("$containerName");
        $propertyValue = $this->getPropertyValue($propertyReflection);

        if(!$property->check($propertyValue)){
            throw new PropertyIsNotValidException($propertyName, $containerName, $this->clazzName);
        }

    }

    #[Override] public function check(string ...$ignoreProperties): void
    {
        $propertiesToIgnore = [...$ignoreProperties];
        foreach ($this->containers as $container) {

            if (in_array($container->getName(), $propertiesToIgnore)) {
                continue;
            }

            foreach ($container->getProperties() as $property) {
                $this->checkOne($container, $property);
            }
        }
    }

    #[Override] public function checkProperty(string $propertyName): void
    {
        foreach ($this->containers as $container) {
            if ($container->getName() === $propertyName) {
                foreach ($container->getProperties() as $property) {
                    $this->checkOne($container, $property);
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