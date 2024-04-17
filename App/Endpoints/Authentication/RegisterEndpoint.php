<?php

namespace Descolar\Endpoints\Authentication;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\App;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Mail\MailManager;
use OpenAPI\Attributes as OA;

class RegisterEndpoint extends AbstractEndpoint
{

    #[Get('auth/verifyAccount/:token', variables: ["token" => RouteParam::STRING], name: 'verifyAccount', auth: false)]
    #[OA\Get(
        path: '/auth/verifyAccount/{token}',
        summary: 'Verify Account',
        tags: ['Authentication'],
        parameters: [new OA\PathParameter(name: 'token', description: 'Token', required: true)],
        responses: [
            new OA\Response(response: 200, description: 'Account verified'),
            new OA\Response(response: 404, description: 'Account not found'),
        ],
    )]
    private function verifyAccount(string $token): void
    {
        $user = App::getOrmManager()->connect()->getRepository(User::class)->verifyToken($token);

        try {

            $pageContent = file_get_contents(DIR_ROOT . '/App/Endpoints/Views/Auth/verify_account_success.html');
            echo $pageContent;


        } catch (EndpointException $e) {
            $pageContent = file_get_contents(DIR_ROOT . '/App/Endpoints/Views/Auth/verify_account_failed.html');
            echo $pageContent;
        }

    }

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

        try {

            $token = bin2hex(random_bytes(32));

            /** @var User $user */
            $user = App::getOrmManager()->connect()->getRepository(User::class)->createUser($username, $password, $firstname, $lastname, $mail, $formation_id, $dateofbirth, $token);
            JsonBuilder::build()
                ->setCode(200)
                ->addData('message', 'Register success')
                ->addData('user', App::getOrmManager()->connect()->getRepository(User::class)->toJson($user))
                ->getResult();

            MailManager::build()
                ->setFrom('contact@descolar.fr')
                ->addTo($user->getMail())
                ->setSMTP()
                ->setSubject('[Descolar] Confirmation de votre inscription')
                ->setBody(true, static function () use ($token) {
                    $mailTemplate = file_get_contents(DIR_ROOT . '/App/Adapters/Mail/Templates/confirmation_mail.html');

                    return str_replace(
                        ['CONFIRMATION_LINK'],
                        ["https://internal-api.descolar.fr/v1/auth/verifyAccount/$token"],
                        $mailTemplate
                    );
                })
                ->send();

        } catch (EndpointException $e) {
            JsonBuilder::build()
                ->setCode($e->getCode())
                ->addData('message', $e->getMessage())
                ->getResult();
        }
    }
}