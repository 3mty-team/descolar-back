<?php

namespace Descolar\Adapters\Router\Annotations;

use Attribute;
use Descolar\Managers\Router\Annotations\Link;

/**
 * Annotation for GET requests
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Get extends Link
{

    /**
     * @see Link::getMethod()
     */
    public final function getMethod(): string
    {
        return 'GET';
    }
}