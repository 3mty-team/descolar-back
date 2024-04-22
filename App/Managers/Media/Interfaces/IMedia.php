<?php

namespace Descolar\Managers\Media\Interfaces;

interface IMedia
{

    /**
     * @return string the name of the media
     */
    public function getName(): string;

    /**
     * @return IMediaType the type of the media
     */
    public function  getType(): IMediaType;

    /**
     * @return string the url of the media
     */
    public function getUrl(): string;

    /**
     * @return array<int, int> the resolution of the media
     */
    public function getSize(): array;

    /**
     * @return int the weight of the media
     */
    public function getWeight(): int;

}