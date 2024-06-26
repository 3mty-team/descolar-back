<?php

namespace Descolar\Adapters\Validator\Annotations;

use Attribute;
use Descolar\Managers\Validator\Annotations\Property;

/*
 * Validator Property, Set up the property to be validated.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Validate extends Property
{
    public function __construct(
        private readonly string $name = '',
    )
    {
    }

    #[\Override] public function check(mixed $content): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }
}