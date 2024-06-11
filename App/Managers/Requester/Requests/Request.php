<?php

namespace Descolar\Managers\Requester\Requests;

use Throwable;

class Request
{
    /**
     * @param string $name The index of the request
     * @param string|null $defaultValue The default value of the request
     * @param class-string<Throwable>|null $toThrowIfNotExists The exception to throw if the key does not exist
     */
    public function __construct(
        protected string       $name,
        protected ?string       $defaultValue = null,
        protected ?string      $toThrowIfNotExists = null
    )
    {
    }

    /**
     * Get the key of the wanted request
     *
     * @return string The name of the request
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the default value of the request
     *
     * @return string|null The default value of the request if key not exists
     */
    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * Get the exception to throw if the key does not exist
     *
     * @return string|null The exception to throw if the key does not exist
     */
    public function getToThrowIfNotExists(): ?string
    {
        return $this->toThrowIfNotExists;
    }

}