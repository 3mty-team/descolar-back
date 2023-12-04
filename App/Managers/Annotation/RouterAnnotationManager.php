<?php


namespace Descolar\Managers\Annotation;

use Descolar\Managers\Endpoint\Interfaces\IEndpoint;
use Descolar\Managers\Router\Annotations\Link;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RouterAnnotationManager
{

    /**
     * @var [ILink, ReflectionMethod][] $attributeList
     */
    private static array $_attributeList = [];

    /**
     * @return [ILink, ReflectionMethod][]
     */
    public static function &getAttributeList(): array
    {
        return self::$_attributeList;
    }

    /**
     * @throws ReflectionException
     */
    private static function isSubClassOfLinkAttribute(ReflectionAttribute $attribute): bool
    {
        $reflectionClass = new ReflectionClass(Link::class);
        $attributeClass = new ReflectionClass($attribute->getName());

        return $attributeClass->isSubclassOf($reflectionClass->getName());
    }

    /**
     * @param ReflectionMethod[] $classMethods
     * @param string $className
     * @return void
     * @throws ReflectionException
     */
    private static function retrieveAttributesFromClass(array $classMethods, string $className): void
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
     * @throws ReflectionException
     */
    public static function getClassesWithLinkAttributes($directory): void
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
            self::retrieveAttributesFromClass($methods, $className);
        }
    }
}