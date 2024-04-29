<?php

namespace Descolar\Managers\Media\Interfaces;

use Descolar\Data\Entities\Media\MediaType;

interface IMediaType
{

    /**
     * Get the ORM Media Type from the IMedia Media Type
     * @return MediaType|null the media type
     */
    public function toMediaType(): ?MediaType;

    /**
     * Get the string representation of the media type
     * @return string the string representation of the media type
     */
    public function __toString(): string;

}