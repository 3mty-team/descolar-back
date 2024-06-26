<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property is in a list of values.
 */

#[Attribute(Attribute::TARGET_PROPERTY)]
class InList extends Property
{

    private readonly array $arrayToAccept;

    /**
     * @param string ...$valuesToAccept Values to accept (No values to accept all values)
     */
    public function __construct(
        mixed ...$valuesToAccept
    )
    {
        $this->arrayToAccept = [...$valuesToAccept];
    }

    #[Override] public function check(mixed $content): bool
    {
        if(empty($this->arrayToAccept)) {
            return true;
        }

        return in_array($content, $this->arrayToAccept);
    }
}