<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property has a length (string) between min and max.
 * /!\ If the content is null, the check will return true.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Length extends Property
{

    /**
     * @param int $min Minimum length (-1 to ignore)
     * @param int $max Maximum length (-1 to ignore)
     */
    public function __construct(
        private readonly int $min = -1,
        private readonly int $max = -1
    ) {
    }
    #[Override] public function check(mixed $content): bool
    {
        if(is_null($content)) {
            return true;
        }

        $minChecked = $this->min === -1 || strlen($content) >= $this->min;
        $maxChecked = $this->max === -1 || strlen($content) <= $this->max;

        return $minChecked && $maxChecked;
    }
}