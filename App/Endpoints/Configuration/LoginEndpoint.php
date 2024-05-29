<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class LoginEndpoint extends AbstractEndpoint
{
    #[Post('/config/login', name: 'login', auth: false)]
    #[OA\Post(
        path: '/config/login',
        summary: 'Login',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 200, description: 'Login success'),
            new OA\Response(response: 403, description: 'User is permanently disabled'),
            new OA\Response(response: 404, description: 'Login failed'),
        ],
    )]
    private function login(): void
    {
        $this->reply(function ($response) {
            $username = $_POST['username'] ?? "";
            $password = $_POST['password'] ?? "";

            $user = OrmConnector::getInstance()->getRepository(Login::class)->getLoginInformation($username, $password);
            OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disableDeactivation($user);

            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJson($user);
            foreach ($userData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}