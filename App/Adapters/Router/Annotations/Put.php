<?php

namespace Descolar\Adapters\Router\Annotations;

use Attribute;
use Override;
use Descolar\Managers\Router\Annotations\Link;

/**
 * Annotation for PUT requests
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Put extends Link
{

    #[Override]
    public final function getMethod(): string
    {
        return 'PUT';
    }
}