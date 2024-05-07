<?php

namespace Descolar\Adapters\Websocket\Components;

use Exception;
use Override;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

abstract class AbstractComponent implements MessageComponentInterface
{

    protected $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }


    #[Override] function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
    }

    #[Override] function onError(ConnectionInterface $conn, Exception $e): void
    {
        $conn->close();
    }
}