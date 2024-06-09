<?php

namespace Descolar\Endpoints\Authentication;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Mail\MailManager;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;

class RegisterEndpoint extends AbstractEndpoint
{
    private function buildMail(User $user): void
    {
        MailManager::build()
            ->setFrom('contact@descolar.fr', "Descolar")
            ->addTo($user->getMail())
            ->setSMTP()
            ->setSubject('[Descolar] Confirmation de votre inscription')
            ->setBody(true, static function () use ($user) {
                $mailTemplate = file_get_contents(DIR_ROOT . '/App/Adapters/Mail/Templates/confirmation_mail.html');

                return str_replace(
                    ['CONFIRMATION_LINK'],
                    ["https://internal-api.descolar.fr/v1/auth/verifyAccount/{$user->getToken()}"],
                    $mailTemplate
                );
            })
            ->send();
    }

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

        /** Its endpoint joined in HTTP request (web browser) **/

        /** @var User $user */
        $user = OrmConnector::getInstance()->getRepository(User::class)->verifyToken($token);

        try {

            $pageContent = file_get_contents(DIR_ROOT . '/App/Endpoints/Views/Auth/verify_account_success.html');
            echo $pageContent;

        } catch (EndpointException $ignored) {
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
        $this->reply(function ($response) {
            [$username, $password, $firstName, $lastName, $mail, $formationId, $dateOfBirth, $profilePath, $bannerPath] = Requester::getInstance()->trackMany(
                "username", "password", "firstname", "lastname", "mail", "formation_id", "dateofbirth", "profile_path", "banner_path"
            );

            /** @var User $user */
            $user = OrmConnector::getInstance()->getRepository(User::class)->createUser($username, $password, $firstName, $lastName, $mail, $formationId, $dateOfBirth, $profilePath, $bannerPath);

            $response->addData('user', OrmConnector::getInstance()->getRepository(User::class)->toJson($user));

            $this->buildMail($user);
        });
    }
}