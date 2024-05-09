<?php

use Descolar\Managers\Websocket\WebsocketManager;

error_reporting(E_ALL & ~E_DEPRECATED);
WebsocketManager::getInstance()->create();
WebsocketManager::getInstance()->add("/");
WebsocketManager::getInstance()->run();
