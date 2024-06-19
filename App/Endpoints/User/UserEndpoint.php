<?php

namespace Descolar\Endpoints\User;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;

class UserEndpoint extends AbstractEndpoint
{

    #[Get('/user/:userUUID', variables: ["userUUID" => RouteParam::UUID], name: 'User', auth: true)]
    #[OA\Get(
        path: "/user/{userUUID}",
        summary: "Get user",
        tags: ["User"],
        parameters: [
            new OA\PathParameter(
                name: "userUUID",
                description: "User UUID",
                in: "path",
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User found"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    private function getUser(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);
            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJson($user);

            foreach ($userData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Get('/user/:userUUID/min', variables: ["userUUID" => RouteParam::UUID], name: 'User', auth: false)]
    #[OA\Get(
        path: "/user/{userUUID}/min",
        summary: "Get user username, first name and last name",
        tags: ["User"],
        parameters: [
            new OA\PathParameter(
                name: "userUUID",
                description: "User UUID",
                in: "path",
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User found"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    private function getUserNames(string $userUUID): void
    {

        $this->reply(function ($response) use ($userUUID) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);
            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJsonNames($user);

            foreach ($userData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Put("/user", name: "UpdateUser", auth: true)]
    #[OA\Put(
        path: "/user",
        summary: "Update user",
        tags: ["User"],
        responses: [
            new OA\Response(response: 200, description: "User updated"),
            new OA\Response(response: 403, description: "User not logged"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    private function updateUser(): void
    {
        $this->reply(function ($response) {
            [$username, $profilePath, $bannerPath, $firstname, $lastname, $biography, $formationId, $sendTimestamp] = Requester::getInstance()->trackMany(
                "username", "profile_path", "banner_path", "firstname", "lastname", "biography", "formation_id", "send_timestamp"
            );

            $user = OrmConnector::getInstance()->getRepository(User::class)->editUser(
                $username,
                $profilePath,
                $bannerPath,
                $firstname,
                $lastname,
                $biography,
                $formationId,
                $sendTimestamp);
            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJson($user);

            foreach ($userData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Put('/user/disable', name: 'disableUser', auth: true)]
    #[OA\Put(
        path: "/user/disable",
        summary: "Disable user",
        tags: ["User"],
        responses: [
            new OA\Response(response: 200, description: "User disabled"),
            new OA\Response(response: 403, description: "User not logged")
        ]
    )]
    private function disableUser(): void
    {
        $this->reply(function ($response) {
            $userUUIDDisabled = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disable();

            $response->addData('id', $userUUIDDisabled);
        });
    }

    #[Put('/user/disable/forever/:userUUID', variables: ["userUUID" => RouteParam::UUID], name: 'disableUserForever', auth: false)]
    #[OA\Put(
        path: "/user/disable/forever/{userUUID}",
        summary: "Disable user forever",
        tags: ["User"],
        parameters: [
            new OA\PathParameter(
                name: "userUUID",
                description: "User UUID",
                in: "path",
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User disabled"),
            new OA\Response(response: 403, description: "User not logged")
        ]
    )]
    private function disableUserForever(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
            $userUUIDDisabled = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disableForever($user);

            $response->addData('id', $userUUIDDisabled);
        });
    }

    #[Put('/user/:userUUID/unban', variables: ["userUUID" => RouteParam::UUID], name: 'unbanUser', auth: false)]
    #[OA\Put(
        path: "/user/unban/{userUUID}",
        summary: "Unban user",
        tags: ["User"],
        parameters: [
            new OA\PathParameter(
                name: "userUUID",
                description: "User UUID",
                in: "path",
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User unbanned"),
            new OA\Response(response: 403, description: "User not logged")
        ]
    )]
    private function unbanUser(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
            $userUUIDUnbanned = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disableDeactivation($user);

            $response->addData('uuid', $userUUIDUnbanned);
        });
    }

    #[Delete('/user', name: 'deleteUser', auth: true)]
    #[OA\Delete(
        path: "/user",
        summary: "Delete user",
        tags: ["User"],
        responses: [
            new OA\Response(response: 200, description: "User deleted"),
            new OA\Response(response: 403, description: "User not logged"),
        ]
    )]
    private function deleteUser(): void
    {
        $this->reply(function ($response) {
            $UUID = OrmConnector::getInstance()->getRepository(User::class)->deleteUser();

            $response->addData('uuid', $UUID);
        });
    }
}