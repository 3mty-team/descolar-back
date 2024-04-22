<?php

namespace Descolar\Adapters\Media;

use Descolar\Managers\Media\Interfaces\IMedia;
use Descolar\Managers\Media\Interfaces\IMediaType;

readonly class Media implements IMedia
{

    public function __construct(
        private string     $name,
        private IMediaType $type,
        private string     $url,
        private array      $size,
        private int        $weight
    )
    {

    }

    #[\Override] public function getName(): string
    {
        return $this->name;
    }

    #[\Override] public function getType(): IMediaType
    {
        return $this->type;
    }

    #[\Override] public function getUrl(): string
    {
        return $this->url;
    }

    #[\Override] public function getSize(): array
    {
        return $this->size;
    }

    #[\Override] public function getWeight(): int
    {
        return $this->weight;
    }
}