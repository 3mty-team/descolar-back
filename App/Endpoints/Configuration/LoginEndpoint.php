<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
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
            new OA\Response(response: 404, description: 'Login failed'),
        ],
    )]
    private function login(): void
    {
        $response = JsonBuilder::build();

        try {
            $username = $_POST['username'] ?? "";
            $password = $_POST['password'] ?? "";

            $user = OrmConnector::getInstance()->getRepository(Login::class)->getLoginInformation($username, $password);
            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJson($user);

            foreach ($userData as $key => $value) {
                $response->addData($key, $value);
            }

            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}