<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;

class LoginEndpoint extends AbstractEndpoint
{
    #[Post('/config/login', name: 'login', auth: false)]
    #[OA\Post(
        path: '/config/login',
        summary: 'Login',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]

    private function login(): void
    {
        $username = $_POST['username'] ?? "";
        $password = $_POST['password'] ?? "";

        if($username != null && $username != '' && $password != null && $password != '') {
            /*
             * @var User $user
             */
            $user = App::getOrmManager()->connect()->getRepository(Login::class)->getLoginInformation($username, $password);
            if ($user == null) {
                JsonBuilder::build()
                    ->setCode(404)
                    ->addData('message', 'Login failed')
                    ->getResult();
                return;
            }
            JsonBuilder::build()
                ->setCode(200)
                ->addData('message', 'Login success')
                ->addData('user', App::getOrmManager()->connect()->getRepository(User::class)->toJson($user))
                ->getResult();
        }
        else {
            JsonBuilder::build()
                ->setCode(404)
                ->addData('message', 'Username or Password is not valid')
                ->getResult();
        }
    }
}