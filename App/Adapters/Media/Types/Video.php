<?php

namespace Descolar\Adapters\Media\Types;

use Descolar\Managers\Media\Interfaces\IMediaType;

class Video implements IMediaType
{
    #[\Override] public function getName(): string
    {
        return 'video';
    }
}