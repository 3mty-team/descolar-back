<?php

namespace Descolar\Adapters\Media\Types;

use Descolar\Managers\Media\Interfaces\IMediaType;

class Image implements IMediaType
{
    #[\Override] public function getName(): string
    {
        return 'image';
    }
}