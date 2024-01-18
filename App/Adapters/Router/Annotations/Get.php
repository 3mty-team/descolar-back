<?php

namespace Descolar\Adapters\Router\Annotations;

use Attribute;
use Descolar\Managers\Router\Annotations\Link;
use Override;

/**
 * Annotation for GET requests
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Get extends Link
{

    #[Override]
    public final function getMethod(): string
    {
        return 'GET';
    }
}