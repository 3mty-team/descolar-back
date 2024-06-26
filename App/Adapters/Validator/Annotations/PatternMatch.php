<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property has a valid pattern (starts with, ends with)
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class PatternMatch extends Property
{

    /**
     * @param string $startWith Start with pattern
     * @param string $endWith End with pattern
     */
    public function __construct(
        private readonly string $startWith = '',
        private readonly string $endWith = ''
    ) {
    }
    #[Override] public function check(mixed $content): bool
    {
        $startWithChecked = $this->startWith === '' || str_starts_with($content, $this->startWith);
        $endWithChecked = $this->endWith === '' || str_ends_with($content, $this->endWith);

        return $startWithChecked && $endWithChecked;
    }
}