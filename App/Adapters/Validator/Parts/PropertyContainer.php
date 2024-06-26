<?php

namespace Descolar\Adapters\Validator\Parts;

use Descolar\Managers\Validator\Annotations\Property;

/**
 * A basic record of a property
 */
class PropertyContainer
{

    /**
     * @param string $clazzName the name of the class
     * @param string $name the name of the property (Validate annotation name)
     * @param array{Property} $properties the properties of the class (annotations)
     */
    public function __construct(
        private readonly string $clazzName,
        private readonly string $name,
        private array $properties = [],
    )
    {
    }

    public function getClazzName(): string
    {
        return $this->clazzName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array{Property}
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(Property $property): void
    {
        $this->properties[] = $property;
    }

}