<?php

namespace Descolar\Managers\Websocket\Interfaces;

interface ISocketBuilder
{

    /**
     * Create the websocket server
     * @param int $port The port to create the server on
     */
    function create(int $port = 8080): void;

    /**
     * Run the websocket server
     */
    function run(): void;

    /**
     * Add a route to the websocket server
     * @param string $route The route to add
     * @param ?string $componentName The component to add to the route
     */
    function add(string $route, ?string $componentName): void;

}