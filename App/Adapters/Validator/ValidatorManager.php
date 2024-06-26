<?php

namespace Descolar\Adapters\Validator;

use Descolar\Adapters\Validator\Annotations\Validate;
use Descolar\Adapters\Validator\Exceptions\ClazzDoesNotExistsException;
use Descolar\Adapters\Validator\Exceptions\ValidateAttributeDontExistsException;
use Descolar\Adapters\Validator\Parts\PropertyContainer;
use Descolar\Managers\Validator\Annotations\Property;
use Descolar\Managers\Validator\Interfaces\IValidator;
use Doctrine\ORM\Mapping\Entity;
use Override;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class ValidatorManager implements IValidator
{

    /** @var array{string, PropertyContainer} */
    public static array $containers = [];

    /** @var array{string, ValidatorManager} */
    private static array $_instances = [];

    private object $entity;
    private string $clazzEntity;

    public function __construct()
    {
    }

    /**
     * @return ValidatorManager The instance of the ValidatorManager
     */
    public static function getInstance(object $entity): ValidatorManager
    {
        $clazzEntity = get_class($entity);

        if (!isset(self::$_instances[$clazzEntity])) {
            self::$_instances[$clazzEntity] = self::create($entity);
        }

        return self::$_instances[$clazzEntity];
    }

    /**
     * Generate the name of the property with the class name
     * @param string $propertyName The name of the property
     * @return string The name of the property with the class name
     */
    private function getPropertyName(string $propertyName): string
    {
        return "$this->clazzEntity\\$propertyName";
    }


    /**
     * Set the entity to be validated
     * @param object $entity The entity to be validated
     */
    public function setEntity(object $entity): void
    {
        $this->entity = $entity;
        $this->clazzEntity = get_class($entity);
    }

    /**
     * Set up the validator
     */
    public function setup(): void
    {
        if (!$this->checkClazzEntity()) {
            throw new ClazzDoesNotExistsException("The class $this->clazzEntity is not an existent entity");
        }

        $this->loadValidator();

        ValidatorChecker::buildChecker($this->entity, $this->getContainers());
    }

    /**
     * Create a new instance of the ValidatorManager
     */
    public static function create(object $entity): ValidatorManager
    {
        $vm = new ValidatorManager();
        $vm->setEntity($entity);
        $vm->setup();

        return $vm;
    }

    /**
     * Get the containers of the properties (Pointer)
     * @return array{string, PropertyContainer}
     */
    public function &getContainers(): array
    {
        if (!isset(self::$containers[$this->clazzEntity])) {
            self::$containers[$this->clazzEntity] = [];
        }

        return self::$containers[$this->clazzEntity];
    }


    /**
     * Populate the containers with the properties of the class
     */
    private function loadValidator(): void
    {
        $class = new ReflectionClass($this->entity);
        $properties = $class->getProperties();

        $storedProperties = [];

        foreach ($properties as $property) {
            $propertyName = $this->getPropertyName($property->getName());
            $attributeList = $property->getAttributes();

            foreach ($attributeList as $attribute) {
                $classReflection = new ReflectionClass($attribute->newInstance());

                if ($classReflection->isSubclassOf(Property::class)) {

                    $this->createContainer($attribute, $propertyName);

                    $this->storeProperty($attribute, $propertyName, $storedProperties);
                }

            }
        }

        $this->fillContainers($storedProperties);
    }

    /**
     * Create a container for the property
     * @param ReflectionAttribute $attribute The attribute of the property
     * @param string $propertyName The name of the property
     */
    private function createContainer(ReflectionAttribute $attribute, string $propertyName): void
    {
        if ($attribute->getName() == Validate::class) {

            if(isset($this->getContainers()[$propertyName])) {
                return;
            }

            $validateClass = $attribute->newInstance();

            $propertyContainer = new PropertyContainer($propertyName, $validateClass->getName());
            $this->getContainers()[$propertyName] = $propertyContainer;
        }
    }

    /**
     * Store the property in the list
     * @param ReflectionAttribute $attribute The attribute of the property
     * @param string $propertyName The name of the property
     * @param array<string, ReflectionAttribute[]> $propertyList The list of properties
     */
    private function storeProperty(ReflectionAttribute $attribute, string $propertyName, &$propertyList): void
    {
        if (!isset($propertyList[$propertyName])) {
            $propertyList[$propertyName] = array();
        }

        $propertyList[$propertyName][] = $attribute;
    }

    /**
     * Check if the class is an entity
     * @return bool True if the class is an entity, false otherwise
     */
    private function checkClazzEntity(): bool
    {

        try {
            $class = new ReflectionClass($this->clazzEntity);
        } catch (ReflectionException) {
            return false;
        }

        $attributes = $class->getAttributes();
        $hasAttribute = false;

        foreach ($attributes as $attribute) {
            if ($attribute->getName() == Entity::class) {
                $hasAttribute = true;
                break;
            }
        }

        return $hasAttribute;
    }

    /**
     * Fill the containers with the properties
     * @param array<string, ReflectionAttribute> $properties
     * @return void
     */
    private function fillContainers(array $properties): void
    {

        foreach ($properties as $propertyName => $attributeList) {

            $propertyContainer = $this->getContainers()[$propertyName] ?? null;

            if ($propertyContainer === null) {
                throw new ValidateAttributeDontExistsException($propertyName, $this->clazzEntity);
            }

            foreach ($attributeList as $attribute) {
                $propertyContainer->addProperty($attribute->newInstance());
            }
        }
    }

    #[Override] public function check(string ...$ignoreProperties): void
    {
        ValidatorChecker::getInstance($this->entity)->check(...$ignoreProperties);
    }

    #[Override] public function checkProperty(string $propertyName): void
    {
        ValidatorChecker::getInstance($this->entity)->checkProperty($propertyName);
    }
}