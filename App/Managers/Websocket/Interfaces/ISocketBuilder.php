<?php

namespace Descolar\Managers\Websocket\Interfaces;

interface ISocketBuilder
{

    /**
     * Run the websocket server
     * @param int $port The port to run the server on
     */
    function run(int $port = 8080): void;

    /**
     * Add a route to the websocket server
     * @param string $route The route to add
     * @param ?string $componentName The component to add to the route
     */
    function add(string $route, ?string $componentName): void;

}