<?php

namespace Descolar\Adapters\Validator\Annotations;

use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Annotations\Property;

use Attribute;
use Override;

/*
 * Validator Property, Check if the property is unique.
 */

#[Attribute(Attribute::TARGET_PROPERTY)]
class Unique extends Property
{

    /**
     * @param string $clazzEntity Class of the entity
     * @param string $fieldEntity Field of the entity
     */
    public function __construct(
        private readonly string $clazzEntity,
        private readonly string $fieldEntity,
    )
    {
    }

    #[Override] public function check(mixed $content): bool
    {
        if(!class_exists($this->clazzEntity)) {
            return false;
        }

        $result = OrmConnector::getInstance()->getRepository($this->clazzEntity)->findOneBy([$this->fieldEntity => $content]);

        return $result === null;
    }
}