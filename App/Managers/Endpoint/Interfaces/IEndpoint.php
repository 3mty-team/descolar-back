<?php

namespace Descolar\Managers\Endpoint\Interfaces;

/**
 * Base interface for endpoints
 */
interface IEndpoint
{

    /**
     * Get the instance of the endpoint
     *
     * @return static The instance of the endpoint
     */
    public static function getInstance(): static;

}