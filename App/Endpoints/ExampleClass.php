<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use OpenAPI\Attributes as OA;

/**
 * Example endpoint
 */
class ExampleClass extends AbstractEndpoint
{

    /**
     * Example method, this method will be called when the user access the page "/".
     */ 
    #[Get('/', name: 'indexPage')]
    #[OA\Get(path: "/", summary: "indexPage", tags: ["Example"])]
    #[OA\Response(response: '200', description: 'Example response')]
    private function index(): void
    {
        echo 'Hello World';
    }

}