<?php

namespace Descolar\Managers\Validator\Annotations;

use Attribute;
use Descolar\Managers\Validator\Interfaces\IProperty;

/**
 * Base class for the property (Attribute)
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
abstract class Property implements IProperty
{

}