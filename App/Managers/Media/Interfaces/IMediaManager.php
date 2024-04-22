<?php

namespace Descolar\Managers\Media\Interfaces;

interface IMediaManager
{
    /**
     * Get a media list from $_FILES
     * @return IMedia[] the saved media
     */
    public function saveMediaList(): array;

    /**
     * Remove a media
     * @param IMedia $media the media to be removed
     */
    public function removeMedia(IMedia $media): void;

    /**
     * @return IMedia[] the list of medias
     */
    public function getMedias(): array;

}