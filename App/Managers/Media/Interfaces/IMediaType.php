<?php

namespace Descolar\Managers\Media\Interfaces;

use Descolar\Data\Entities\Media\MediaType;

interface IMediaType
{

    public function toMediaType(): ?MediaType;

    public function __toString(): string;

}