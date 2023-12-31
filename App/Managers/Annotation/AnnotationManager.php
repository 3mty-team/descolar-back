<?php

namespace Descolar\Managers\Annotation;

use Descolar\Managers\Annotation\Exceptions\ClassDontExistException;
use Descolar\Managers\Annotation\Exceptions\NameIsNotSetException;
use Descolar\Managers\Annotation\Interfaces\IAnnotationManager;
use Descolar\Managers\Endpoint\Interfaces\IEndpoint;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class AnnotationManager implements IAnnotationManager
{


    /**
     * @var array<array<IAnnotationManager, array{object, ReflectionMethod}>> $_attributeList The list of ILink attributes
     */
    private static array $_attributeList = [];

    /**
     * @var class-string $_attributeClazz The attribute class to search
     */
    public function __construct(
        private readonly string $_attributeClazz,
    )
    {
        $this->checkAttributeClass();
    }

    /**
     * Check if the attribute class is set
     *
     * @throws ClassDontExistException If the attribute class is not set
     * @throws NameIsNotSetException If the attribute class does not exist
     */
    private function checkAttributeClass(): void
    {
        if ($this->_attributeClazz === null) {
            throw new NameIsNotSetException("Attribute class is not set");
        }

        if (!class_exists($this->_attributeClazz)) {
            throw new ClassDontExistException("Attribute class does not exist");
        }
    }

    /**
     * Check if the attribute is a subclass of {@see $this->_attributeClass}
     *
     * @param ReflectionAttribute $attribute The attribute to check
     * @return bool If the attribute is a subclass of {@see $this->_attributeClass}
     * @throws ReflectionException If the attribute class does not exist
     */
    private function isSubClassOfAttribute(ReflectionAttribute $attribute): bool
    {
        if (!str_contains($attribute->getName(), "Descolar")) {
            return false;
        }

        $reflectionClass = new ReflectionClass($this->_attributeClazz);
        $attributeClass = new ReflectionClass($attribute->getName());

        return $attributeClass->isSubclassOf($reflectionClass->getName()) || $attributeClass->getName() === $reflectionClass->getName();
    }

    /**
     * Retrieve the attributes from the class methods
     *
     * @param ReflectionMethod[] $classMethods The methods of the class
     * @throws ReflectionException If the attribute class does not exist
     */
    private function retrieveAttributesFromClass(array $classMethods): void
    {
        foreach ($classMethods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                if ($this->isSubClassOfAttribute($attribute)) {
                    $this->getAttributeList()[] = [$attribute->newInstance(), $method];
                }
            }
        }
    }

    /**
     * Define the namespace by the path
     *
     * @param string $className The path of the class
     * @return string The path of the class
     */
    private function defineClassName(string $className): string
    {
        $className = str_replace('/', '\\', $className);
        $className = explode('\\', $className);
        $className = array_slice($className, array_search('Endpoints', $className));
        $className = implode('\\', $className);

        $endpointIndex = strpos($className, 'Endpoints');
        $className = substr($className, $endpointIndex);

        return "Descolar\\$className";
    }

    /**
     * Get the reference of the list of {@see $this->_attributeClass} attributes, the key is the {@see $this->_attributeClass} attribute and the value is the method that has the attribute
     *
     * @param ?class-string $annotationClazz The attribute class to search
     *
     * @return array<array{object, ReflectionMethod}> The reference of the list of {@see $this->_attributeClass} attributes
     * @see ReflectionMethod
     */
    public function &getAttributeList(?string $annotationClazz = null): array
    {
        if ($annotationClazz !== null && isset(self::$_attributeList[$annotationClazz])) {
            return self::$_attributeList[$annotationClazz];
        }

        if (!isset(self::$_attributeList[$this->_attributeClazz])) {
            self::$_attributeList[$this->_attributeClazz] = [];
        }

        return self::$_attributeList[$this->_attributeClazz];
    }


    /**
     * Get all the interfaced {@see IEndpoint} classes with Link attributes from the directory
     *
     * @param string $directory The directory to search
     * @throws ReflectionException If the attribute class does not exist
     */
    public function generateAttributes(string $directory): void
    {
        $items = scandir($directory);

        foreach ($items as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = "$directory/$item";
            if (is_dir($path)) {
                $this->generateAttributes($path);
                continue;
            }

            if (!is_file($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            require_once $path;
            $className = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

            $className = $this->defineClassName($className);

            if (!in_array(IEndpoint::class, class_implements($className))) {
                continue;
            }


            $reflectionClass = new ReflectionClass($className);
            $methods = $reflectionClass->getMethods();
            $this->retrieveAttributesFromClass($methods);
        }
    }

}