<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Event\Annotations\Listener;
use Descolar\Managers\Event\Emitter;
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

    /**
     * Another mehtod, this method will be called when the user access the page "/a/eventLink". This method will call an event.
     */
    #[Get('/a/eventLink', name: 'eventLink')]
    #[OA\Get(path: "/a/eventLink", summary: "eventLink", tags: ["Example"])]
    #[OA\Response(response: '200', description: 'link with event')]
    private function indexa(): void
    {
        echo 'Do i work?<br>';
        Emitter::fire('helloEvent', 'aa');
    }

    /**
     * This method will be called when the event "helloEvent" is fired.
     */
    #[Listener('helloEvent')]
    private function privateMethod($p): void
    {
        echo "I'm a basic event listener, I received $p :0";
    }

}