<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Event\Annotations\Listener;
use Descolar\Managers\Event\Emitter;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;

/**
 * Example endpoint
 */
class ExampleClass extends AbstractEndpoint
{

    /**
     * Example method, this method will be called when the user access the page "/".
     */
    #[Get('/', name: 'indexPage', auth: false)]
    #[OA\Get(path: "/", summary: "indexPage", tags: ["Example"])]
    #[OA\Response(response: '200', description: 'Example response')]
    private function index(): void
    {
        echo 'Hello World';
    }

    #[Get('/user/:user/:age', variables: ["user" => ".*?", "age" => ".*?"], name: 'callUser', auth: false)]
    #[OA\Get(path: "/user/call", summary: "callUser", tags: ["User"])]
    #[OA\Response(response: '200', description: 'Call an user and fire an event [not implemented]')]
    private function callUser(string $user, int $age): void
    {

        dump($_GET);
        $userData = [
            'name' => $user,
            'age' => $age
        ];
        JsonBuilder::build()
            ->setCode(200)
            ->addData('user', $userData['name'])
            ->addData('age', $userData['age'])
            ->getResult();

        Emitter::fire('userCalled', $userData['name']);
    }

    /**
     * Another mehtod, this method will be called when the user access the page "/a/eventLink". This method will call an event.
     */
    #[Get('/a/eventLink', name: 'eventLink', auth: true)]
    #[OA\Get(path: "/a/eventLink", summary: "eventLink", tags: ["Example"])]
    #[OA\Response(response: '200', description: 'link with event')]
    private function indexa(): void
    {
        echo 'Do I work?<br>';
        Emitter::fire('helloEvent', 'I work');
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