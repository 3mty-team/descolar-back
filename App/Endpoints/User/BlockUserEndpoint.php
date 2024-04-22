<?php

namespace Descolar\Endpoints\User;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\BlockUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class BlockUserEndpoint extends AbstractEndpoint
{

    #[Get('/user/blocks', name: 'blockList', auth: true)]
    #[OA\Get(
        path: '/user/blocks',
        summary: 'Get user blocks',
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Block List found'),
            new OA\Response(response: 403, description: 'User not logged'),
        ]
    )]
    private function getBlocks(): void
    {
        $response = JsonBuilder::build();

        try {
            $blocks = OrmConnector::getInstance()->getRepository(BlockUser::class)->getBlockList();
            $users = [];

            foreach ($blocks as $value) {
                $users[] = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($value);
            }

            $response->addData('users', $users);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Get('user/:userUUID/block', variables: ["userUUID" => RouteParam::UUID], name: 'isBlockedBy', auth: true)]
    #[OA\Get(
        path: '/user/{userUUID}/block',
        summary: 'Check if user is blocked by logged user',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
                in: 'path',
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Response found'),
            new OA\Response(response: 403, description: 'User not logged'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function isBlockedBy(string $userUUID): void
    {
        $response = JsonBuilder::build();

        try {
            $result = OrmConnector::getInstance()->getRepository(BlockUser::class)->checkBlockedStatus($userUUID);
            $response->addData('result', $result);
            $response->setCode(200);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Post('/user/:userUUID/block', variables: ["userUUID" => RouteParam::UUID], name: 'blockUser', auth: true)]
    #[OA\Post(
        path: '/user/{userUUID}/block',
        summary: 'Block user',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
                in: 'path',
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'User blocked'),
            new OA\Response(response: 403, description: 'User not logged, already blocked or blocking himself'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function blockUser(string $userUUID): void
    {

        $response = JsonBuilder::build();

        try {

            $blockingUser = OrmConnector::getInstance()->getRepository(BlockUser::class)->blockUser($userUUID);
            $blockingUserData = OrmConnector::getInstance()->getRepository(BlockUser::class)->toJson($blockingUser);

            foreach ($blockingUserData as $key => $value) {
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

    #[Delete('/user/:userUUID/block', variables: ["userUUID" => RouteParam::UUID], name: 'unblockUser', auth: true)]
    #[OA\Delete(
        path: '/user/{userUUID}/block',
        summary: 'Unblock user',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
                in: 'path',
                required: true
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'User unblocked'),
            new OA\Response(response: 403, description: 'User not logged, not blocked or unblocking himself'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function unblockUser(string $userUUID): void
    {

        $response = JsonBuilder::build();

        try {

            $blockingUser = OrmConnector::getInstance()->getRepository(BlockUser::class)->unBlockUser($userUUID);
            $blockingUserData = OrmConnector::getInstance()->getRepository(BlockUser::class)->toJson($blockingUser);

            foreach ($blockingUserData as $key => $value) {
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