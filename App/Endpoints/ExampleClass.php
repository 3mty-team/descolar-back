<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;

/**
 * Example endpoint
 */
class ExampleClass extends AbstractEndpoint
{

    /**
     * Example method, this method will be called when the user access the page "/".
     */
    #[Get('/', name: 'indexPage')]
    private function index(): void
    {
        echo 'Hello World';
    }

}