<?php

namespace Descolar\Managers\Event\Interfaces;

interface IEmitter
{

    /**
     * Send an event to the listeners
     * @param class-string $event The event to send
     * @param mixed $params The params to send to the listeners
     */
    public static function fire(string $event, mixed $params): void;

}