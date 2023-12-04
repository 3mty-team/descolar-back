<?php


namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;

class ExampleClass extends AbstractEndpoint
{

    #[Get('/', name: 'indexPage')]
    private function index(): void
    {
        echo 'Hello World';
    }

}