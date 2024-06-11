<?php

namespace Descolar\Managers\Requester;

use Descolar\App;
use Descolar\Managers\Requester\Interfaces\IRequest;

class Requester
{

    /**
     * Get the instance of the requester
     *
     * @return IRequest|null The instance of the requester
     */
    public static function getInstance(): ?IRequest
    {
        return App::getRequester();
    }

}