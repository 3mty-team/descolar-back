<?php

namespace Descolar\Managers\Requester\Requests;

abstract class RequestType
{

    public function __construct(
        protected string $name,
        protected string $method,
    )
    {
    }

    /**
     * Get the instance of the requestType
     *
     * @return RequestType The instance of the requestType
     */
    abstract public static function getInstance(): RequestType;

    /**
     * Get the item from the requestType
     *
     * @param string $key The key of the item
     * @return mixed The item
     */
    abstract public function getItem(string $key): mixed;

    /**
     * Get the name of the requestType
     *
     * @return string The name of the requestType
     */
    public function getName(): string
    {
        return $this->name;
    }

}