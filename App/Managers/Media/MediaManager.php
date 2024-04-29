<?php

namespace Descolar\Managers\Media;

use Descolar\App;
use Descolar\Managers\Media\Interfaces\IMediaManager;

class MediaManager
{

    /**
     * Get the media manager instance
     * @return IMediaManager the media manager instance
     */
    public static function getInstance(): IMediaManager
    {
        return App::getMediaManager();
    }

}