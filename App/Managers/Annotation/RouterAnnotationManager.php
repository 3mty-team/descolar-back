<?php

namespace Descolar\Managers\Annotation;

use Descolar\Managers\Endpoint\Interfaces\IEndpoint;
use Descolar\Managers\Router\Annotations\Link;
use Descolar\Managers\Router\Interfaces\ILink;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RouterAnnotationManager
{

    /**
     * @var array<array{ILink, ReflectionMethod}> $attributeList The list of ILink attributes
     */
    private static array $_attributeList = [];

    /**
     * Get the reference of the list of ILink attributes, the key is the ILink attribute and the value is the method that has the attribute
     *
     * @return array<array{ILink, ReflectionMethod}> The reference of the list of ILink attributes
     * @see ReflectionMethod
     * @see ILink
     */
    public static function &getAttributeList(): array
    {
        return self::$_attributeList;
    }

    /**
     * Check if the attribute is a subclass of {@see Link}
     *
     * @param ReflectionAttribute $attribute The attribute to check
     * @return bool If the attribute is a subclass of Link
     * @throws ReflectionException If the attribute class does not exist
     */
    private static function isSubClassOfLinkAttribute(ReflectionAttribute $attribute): bool
    {
        if(!str_contains($attribute->getName(),"Descolar")) {
            return false;
        }

        $reflectionClass = new ReflectionClass(Link::class);
        $attributeClass = new ReflectionClass($attribute->getName());

        return $attributeClass->isSubclassOf($reflectionClass->getName());
    }

    /**
     * Retrieve the attributes from the class methods
     *
     * @param ReflectionMethod[] $classMethods The methods of the class
     * @throws ReflectionException If the attribute class does not exist
     */
    private static function retrieveAttributesFromClass(array $classMethods): void
    {
        foreach ($classMethods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                if (self::isSubClassOfLinkAttribute($attribute)) {
                    self::getAttributeList()[] = [$attribute->newInstance(), $method];
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
    private static function defineClassName(string $className): string
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
     * Get all the interfaced {@see IEndpoint} classes with Link attributes from the directory
     *
     * @param string $directory The directory to search
     * @throws ReflectionException If the attribute class does not exist
     */
    public static function getClassesWithLinkAttributes(string $directory): void
    {
        $items = scandir($directory);

        foreach ($items as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = "$directory/$item";
            if (is_dir($path)) {
                self::getClassesWithLinkAttributes($path);
                continue;
            }

            if (!is_file($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            require $path;
            $className = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

            $className = self::defineClassName($className);

            if (!in_array(IEndpoint::class, class_implements($className))) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);
            $methods = $reflectionClass->getMethods();
            self::retrieveAttributesFromClass($methods);
        }
    }
}