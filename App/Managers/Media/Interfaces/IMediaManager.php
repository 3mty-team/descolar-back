<?php

namespace Descolar\Managers\Media\Interfaces;

use Descolar\Data\Entities\Media\Media;

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
     * Disable a media
     * @param IMedia $media the media to be disabled
     */
    public function disableMedia(IMedia $media): void;

    /**
     * @return IMedia[] the list of medias
     */
    public function getMedias(): array;

    /**
     * generate a {@link IMedia} from a {@link Media}
     * @param Media $media the media to be converted
     */
    public function generateMedia(Media $media): IMedia;

}