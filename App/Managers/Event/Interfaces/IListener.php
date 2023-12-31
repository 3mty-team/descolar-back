<?php

namespace Descolar\Managers\Event\Interfaces;

interface IListener
{
    /**
     * Return event name, may to be unique.
     *
     * @return string Event name
     */
    public function getName(): string;
}