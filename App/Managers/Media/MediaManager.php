<?php

namespace Descolar\Managers\Media;

use Descolar\App;
use Descolar\Managers\Media\Interfaces\IMediaManager;

class MediaManager
{

    public static function getInstance(): IMediaManager
    {
        return App::getMediaManager();
    }

}