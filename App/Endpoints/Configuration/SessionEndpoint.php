<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;

class SessionEndpoint extends AbstractEndpoint
{
    #[Get('/config/session/:sessionUuid', name: 'Search Session by id', auth: true)]
    private function searchSessionByUuid(String $sessionUuid): void
    {
        $session = App::getOrmManager()->connect()->getRepository(Session::class)->getSessionByUuid($sessionUuid);

        if ($session === null) {
            JsonBuilder::build()
                ->setCode(404)
                ->addData('message', 'Session not found')
                ->getResult();
            return;
        }

        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'Session started')
            ->addData('session', App::getOrmManager()->connect()->getRepository(Session::class)->toJson($session))
            ->getResult();
    }

    #[Post('/config/session', name: 'Create Session', auth: true)]
    private function createSession(): void
    {
        $date = $_POST['date'] ?? "";
        $localisation = $_POST['localisation'] ?? "";
        $userAgent = $_POST['user_agent'] ?? "";

        if (empty($date) || empty($localisation) || empty($userAgent)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Missing parameters')
                ->getResult();
            return;
        }

        $session = App::getOrmManager()->connect()->getRepository(Session::class)->createSessionn($date, $localisation, $userAgent);

        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'Session started')
            ->addData('session', App::getOrmManager()->connect()->getRepository(Session::class)->toJson($session))
            ->getResult();
    }
}