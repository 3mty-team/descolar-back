<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\Parameter;

class SessionEndpoint extends AbstractEndpoint
{
    #[Get('/config/session/:sessionUuid', variables: ["sessionUUID" => RouteParam::NUMBER], name: 'Search Session by id', auth: true)]
    #[OA\Get(path: "/config/session/{sessionUuid}", summary: "Search Session by id", tags: ["Configuration"], parameters: [new Parameter("sessionUuid", "sessionUuid", "Session UUID", required: true)], responses: [new OA\Response(response: 201, description: "Session started"), new OA\Response(response: 404, description: "Session not found")])]
    private function searchSessionByUuid(string $sessionUuid): void
    {
        $this->reply(function ($response) use ($sessionUuid) {
            $session = OrmConnector::getInstance()->getRepository(Session::class)->getSessionByUuid($sessionUuid);
            $sessionData = OrmConnector::getInstance()->getRepository(Session::class)->toJson($session);

            foreach ($sessionData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Post('/config/session', name: 'Create Session', auth: true)]
    #[OA\Post(
        path: '/config/session',
        summary: 'Create Session',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 201, description: 'Session started'),
            new OA\Response(response: 400, description: 'Missing parameters'),
        ]
    )]
    private function createSession(): void
    {
        $this->reply(function ($response) {

            $date = $_POST['date'] ?? "";
            $localisation = $_POST['localisation'] ?? "";
            $userAgent = $_POST['user_agent'] ?? "";

            $session = OrmConnector::getInstance()->getRepository(Session::class)->createSession($date, $localisation, $userAgent);
            $sessionData = OrmConnector::getInstance()->getRepository(Session::class)->toJson($session);

            foreach ($sessionData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}