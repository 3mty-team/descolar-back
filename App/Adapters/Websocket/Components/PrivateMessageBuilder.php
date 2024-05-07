<?php

namespace Descolar\Adapters\Websocket\Components;

use Override;
use Ratchet\ConnectionInterface;

class PrivateMessageBuilder extends AbstractComponent
{

    #[Override] function onOpen(ConnectionInterface $conn): void
    {
        $data = json_encode($conn, true);

        $userUUID = $data['userUUID'] ?? null;

        if($userUUID === null) {
            $conn->send(json_encode([
                'error' => 'User UUID not provided'
            ]));

            return;
        }

        $this->clients->attach($conn, $userUUID);

        $conn->send(json_encode([
            'message' => 'Connected'
        ]));
    }

    #[Override] function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);
        $toSendUUID = $data['to'] ?? null;

        if($toSendUUID === null) {
            $from->send(json_encode([
                'error' => 'User UUID not provided'
            ]));

            return;
        }

        if($this->clients->offsetExists($toSendUUID)) {
            $client = $this->clients->offsetGet($toSendUUID);
            $client->send(json_encode([
                'message' => $data['message']
            ]));

            return;
        }

        $from->send(json_encode([
            'error' => 'User not found'
        ]));

    }
}