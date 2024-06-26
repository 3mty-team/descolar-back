<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property has a valid regex.
 */

#[Attribute(Attribute::TARGET_PROPERTY)]
class Regex extends Property
{

    public function __construct(
        private readonly string $regex
    )
    {
    }

    #[Override] public function check(mixed $content): bool
    {
        return preg_match($this->regex, $content) === 1;
    }
}