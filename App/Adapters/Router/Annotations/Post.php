<?php

namespace Descolar\Adapters\Router\Annotations;

use Attribute;
use Descolar\Managers\Router\Annotations\Link;
use Override;

/**
 * Annotation for POST requests
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Post extends Link
{

    #[Override]
    public final function getMethod(): string
    {
        return 'POST';
    }
}