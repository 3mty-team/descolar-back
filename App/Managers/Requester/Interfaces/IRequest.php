<?php

namespace Descolar\Managers\Requester\Interfaces;

use Descolar\Managers\Requester\Requests\Request;
use Descolar\Managers\Requester\Requests\RequestType;

interface IRequest
{

    /**
     * Track a request (POST, GET, PUT, DELETE, etc.) and return the value
     * @param string|array{string, string} $request The request to track, can be an array to define a default value $key or [$key, $defaultValue]
     * @return mixed The value of the key requested
     */
    public function trackOne(string|array $request): mixed;

    /**
     * Track many requests and return the values
     * @param string|array ...$name The requests to track
     * @return array The values of the keys requested
     *
     * @uses trackOne
     */
    public function trackMany(string|array ...$name): array;

    /**
     * Track a request and return the value, request can have many parameters like default value or exception to throw
     * @param Request $request The request to track
     * @return mixed The value of the key requested
     */
    public function trackRequest(Request $request): mixed;

    /**
     * Get the actual request type (POST, GET, PUT, DELETE, etc.)
     * @return RequestType The request type
     */
    public function getRequestType(): RequestType;

}