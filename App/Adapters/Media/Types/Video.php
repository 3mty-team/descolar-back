<?php

namespace Descolar\Adapters\Media\Types;

use Descolar\Data\Entities\Media\MediaType;
use Descolar\Managers\Media\Interfaces\IMediaType;

class Video implements IMediaType
{
    public function __toString(): string
    {
        return 'video';
    }

    #[\Override] public function toMediaType(): ?MediaType
    {
        return MediaType::VIDEO;
    }
}