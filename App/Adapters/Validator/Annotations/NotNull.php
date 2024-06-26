<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property is not null or empty.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotNull extends Property
{
    #[Override] public function check(mixed $content): bool
    {
        return $content !== null && $content !== '' && $content !== 0 && $content !== '[]';
    }
}