<?php

namespace Descolar\Managers\Websocket;

use Descolar\App;
use Descolar\Managers\Websocket\Interfaces\ISocketBuilder;

class WebsocketManager
{
    public static function getInstance(): ISocketBuilder
    {
        return App::getSocketManager()->getInstance();
    }
}