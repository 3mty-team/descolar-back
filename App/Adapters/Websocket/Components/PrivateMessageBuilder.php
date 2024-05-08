<?php

namespace Descolar\Adapters\Websocket\Components;

use Descolar\Adapters\Websocket\MessageManager;
use Descolar\Adapters\Websocket\Utils\ConsoleColor;
use Descolar\Adapters\Websocket\Utils\PrivateMessageObjectStorage;
use Descolar\Adapters\Websocket\Utils\StringObject;
use Override;
use Ratchet\ConnectionInterface;

class PrivateMessageBuilder extends AbstractComponent
{

    /** @var array<string, array> $cachedMessages (toUUID => ($data)) */
    private array $cachedMessages = array();

    public function __construct()
    {
        parent::__construct();
        $this->clients = new PrivateMessageObjectStorage();
    }

    private function register(ConnectionInterface $from, array $data): void
    {
        $uuid = $data['uuid'] ?? null;
        if ($uuid === null) {
            $from->send(json_encode([
                'error' => 'UUID not provided'
            ]));

            return;
        }

        $uuidObject = new StringObject($uuid);

        if ($this->clients->offsetExists($uuidObject)) {
            return;
        }
        $this->clients->offsetSet($uuidObject, $from);

        $from->send(json_encode([
            'message' => 'Registered'
        ]));

        if (array_key_exists($uuid, $this->cachedMessages)) {
            foreach ($this->cachedMessages[$uuid] as $cachedMessage) {
                $from->send(json_encode([
                    'from' => $cachedMessage['data']['from'],
                    'message' => $cachedMessage['data']['message']
                ]));
                unset($cachedMessage);
            }
        }
    }

    private function send(ConnectionInterface $from, array $data): void
    {
        $toUUID = $data['to'] ?? null;
        $fromUUID = $data['from'] ?? null;
        $message = $data['message'] ?? null;

        if ($toUUID === null || $fromUUID === null || $message === null) {
            $from->send(json_encode([
                'error' => 'Invalid data'
            ]));
            return;
        }

        $uuidObject = new StringObject($toUUID);
        if (!$this->clients->offsetExists($uuidObject)) {
            echo MessageManager::getPrefix() . "Client not found, caching message...\n";
            $this->cachedMessages[$toUUID][] = [
                'data' => $data
            ];
            return;
        }

        echo MessageManager::getPrefix() . "Sending message...\n";
        $client = $this->clients->offsetGet($uuidObject);
        $client->send(json_encode([
            'from' => $data['from'],
            'message' => $data['message']
        ]));
    }

    #[Override] function onOpen(ConnectionInterface $conn): void
    {
        echo MessageManager::getPrefix() . "New connection...\n";


        $conn->send(json_encode([
            'message' => 'Connected'
        ]));
    }

    #[Override] function onMessage(ConnectionInterface $from, $msg): void
    {
        echo MessageManager::getPrefix() . "New message...\n";

        $data = json_decode($msg, true);

        //get method to perform
        $method = $data['method'] ?? null;

        match ($method) {
            'register' => $this->register($from, $data),
            'send' => $this->send($from, $data),
            default => $from->send(json_encode([
                'error' => 'Method not found'
            ]))
        };

    }

    #[Override] function onClose(ConnectionInterface $conn): void
    {
        echo MessageManager::getPrefix() . "Connection closed...\n";

        $this->clients->rewind();

        while ($this->clients->valid()) {
            $uuid = $this->clients->current();
            $client = $this->clients->offsetGet($uuid);
            if ($client === $conn) {
                $this->clients->detach($uuid);
                return;
            }

            $this->clients->next();
        }

        echo "Client not found\n";
    }
}