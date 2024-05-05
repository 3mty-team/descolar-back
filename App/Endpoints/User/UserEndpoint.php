<?php

namespace Descolar\Endpoints\User;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
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

        $response = JsonBuilder::build();

        try {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);
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

        $response = JsonBuilder::build();

        try {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);
            $userData = OrmConnector::getInstance()->getRepository(User::class)->toJsonNames($user);

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
        global $_REQ;
        RequestUtils::cleanBody();
        $response = JsonBuilder::build();

        $username = $_REQ['username'] ?? "";
        $profilePath = $_REQ['profile_path'] ?? "";
        $firstname = $_REQ['firstname'] ?? "";
        $lastname = $_REQ['lastname'] ?? "";
        $biography = $_REQ['biography'] ?? "";
        $formationId = $_REQ['formation_id'] ?? 0;
        $sendTimestamp = $_REQ['send_timestamp'] ?? 0;

        try {
            $user = OrmConnector::getInstance()->getRepository(User::class)->editUser(
                $username,
                $profilePath,
                $firstname,
                $lastname,
                $biography,
                $formationId,
                $sendTimestamp);
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

    #[Put('user/disable', name: 'disableUser', auth: true)]
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

        $response = JsonBuilder::build();

        try {
            $userId = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disable();

            $response->addData('id', $userId);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

    #[Put('user/disable/forever', name: 'disableUserForever', auth: true)]
    #[OA\Put(
        path: "/user/disable/forever",
        summary: "Disable user forever",
        tags: ["User"],
        responses: [
            new OA\Response(response: 200, description: "User disabled"),
            new OA\Response(response: 403, description: "User not logged")
        ]
    )]
    private function disableUserForever(): void
    {
        $response = JsonBuilder::build();

        try {
            $userId = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->disableForever();

            $response->addData('id', $userId);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

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

        $response = JsonBuilder::build();

        try {
            $UUID = OrmConnector::getInstance()->getRepository(User::class)->deleteUser();

            $response->addData('uuid', $UUID);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }


}