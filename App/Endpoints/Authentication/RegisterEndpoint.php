<?php

namespace Descolar\Endpoints\Authentication;

use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\RequestBody;

class RegisterEndpoint extends AbstractEndpoint
{
    #[Post('/auth/register', name: 'login', auth: false)]
    #[OA\Post(
        path: '/auth/register',
        summary: 'Register',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Not Found'),
        ],
    )]
    private function register(): void
    {
        $username = $_POST['username'] ?? "";
        $password = $_POST['password'] ?? "";
        $firstname = $_POST['firstname'] ?? "";
        $lastname = $_POST['lastname'] ?? "";
        $mail = $_POST['mail'] ?? "";
        $formation_id = $_POST['formation_id'] ?? "";
        $dateofbirth = $_POST['dateofbirth'] ?? "";

        try  {
            /*
         * @var User $user
         */
            $user = App::getOrmManager()->connect()->getRepository(User::class)->createUser($username, $password, $firstname, $lastname, $mail, $formation_id, $dateofbirth);
            JsonBuilder::build()
                ->setCode(200)
                ->addData('message', 'Register success')
                ->addData('user', App::getOrmManager()->connect()->getRepository(User::class)->toJson($user))
                ->getResult();
        } catch (EndpointException $e) {
            JsonBuilder::build()
                ->setCode($e->getCode())
                ->addData('message', $e->getMessage())
                ->getResult();
        }
    }
}