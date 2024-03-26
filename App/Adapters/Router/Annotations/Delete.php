<?php

namespace Descolar\Adapters\Router\Annotations;

use Attribute;
use Override;
use Descolar\Managers\Router\Annotations\Link;

/**
 * Annotation for DELETE requests
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Delete extends Link
{

    #[Override]
    public final function getMethod(): string
    {
        return 'DELETE';
    }
}