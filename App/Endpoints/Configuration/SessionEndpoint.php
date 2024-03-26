<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;

class SessionEndpoint extends AbstractEndpoint
{
    #[Post('/config/session/:sessionId', name: 'Create Session', auth: false)]
    #[OA\Post(
        path: '/config/session/:sessionId',
        summary: 'Create Session',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    private function createSession(): void
    {
        $date = $_POST['date'];
        $localisation = $_POST['localisation'];
        $userAgent = $_POST['user_agent'];

        if (empty($date) || empty($localisation) || empty($userAgent)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Missing parameters')
                ->getResult();
            return;
        }

        App::getOrmManager()->connect()->getRepository(SessionEndpoint::class)->createSession($date, $localisation, $userAgent);

        JsonBuilder::build()
            ->setCode(200)
            ->addData('message', 'Session started')
            ->getResult();
    }
}