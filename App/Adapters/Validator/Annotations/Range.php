<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property is in a range (number).
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Range extends Property
{

    /**
     * @param int $min Minimum value (-1 to ignore)
     * @param int $max Maximum value (-1 to ignore)
     */
    public function __construct(
        private readonly int $min = -1,
        private readonly int $max = -1
    ) {
    }
    #[Override] public function check(mixed $content): bool
    {
        if(is_int($content) || is_float($content)) {
            return false;
        }

        $minChecked = $this->min === -1 || (int) $content >= $this->min;
        $maxChecked = $this->max === -1 || (int) $content <= $this->max;

        return $minChecked && $maxChecked;
    }
}