<?php

namespace Descolar\Managers\Annotation\Interfaces;

use ReflectionMethod;

interface IAnnotationManager
{
    /**
     * List all attributes of the given clazz
     *
     * @param string|null $annotationClazz The annotation class to search
     * @return array<array{object, ReflectionMethod}> The list of attributes
     */
    public function &getAttributeList(?string $annotationClazz): array;

    /**
     * Generate the attributes of the given directory and save them in a pointed list (array)
     *
     * @param string $directory The directory to search
     */
    public function generateAttributes(string $directory): void;
}