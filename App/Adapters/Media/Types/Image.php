<?php

namespace Descolar\Adapters\Media\Types;

use Descolar\Data\Entities\Media\MediaType;
use Descolar\Managers\Media\Interfaces\IMediaType;

class Image implements IMediaType
{


    public function __toString(): string
    {
        return 'image';
    }

    #[\Override] public function toMediaType(): ?MediaType
    {
        return MediaType::IMAGE;
    }
}