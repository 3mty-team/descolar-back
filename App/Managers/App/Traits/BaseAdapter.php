<?php

namespace Descolar\Managers\App\Traits;

use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\InterfaceString;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

trait BaseAdapter
{

    /**
     * Set the singleton adapter to be used by the application.
     *
     * @param mixed $singleton the singleton to be set. (Parameter passed by reference)
     * @param class-string<RuntimeException> $exceptionClazz the exception to be thrown if the singleton is not found or if he doesn't extend {@see $subclassClazz} interface.
     * @param class-string<InterfaceString> $subclassClazz the interface that the singleton must extend.
     * @param class-string<ClassString> $reflectedClazz the singleton class.
     */
    private static function useAdapter(mixed &$singleton, string $exceptionClazz, string $subclassClazz, string $reflectedClazz): void
    {

        if (!class_exists($reflectedClazz) || !is_subclass_of($reflectedClazz, $subclassClazz)) {
            throw new $exceptionClazz();
        }

        try {
            $classReflection = new ReflectionClass($reflectedClazz);
            $singleton = $classReflection->newInstance();
        } catch (ReflectionException) {
            throw new $exceptionClazz();
        }

    }

    /**
     * Return the singleton, if it is set, from adapters.
     *
     * @param mixed $singleton the singleton to be returned. (Parameter passed by reference)
     * @param class-string<RuntimeException> $exceptionClazz the exception to be thrown if the singleton is not set.
     * @return mixed the singleton.
     */
    private static function getAdapter(mixed &$singleton, string $exceptionClazz): mixed
    {
        if (is_null($singleton)) {
            throw new $exceptionClazz();
        }

        return $singleton;
    }

}