<?php

namespace Descolar\Endpoints\User;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\FollowUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Adapters\Router\Annotations\Get;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class FollowUserEndpoint extends AbstractEndpoint
{

    #[Get('/user/followers', name: 'followerList', auth: true)]
    #[OA\Get(
        path: '/user/followers',
        summary: 'Get user followers',
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Follower List found'),
            new OA\Response(response: 403, description: 'User not logged'),
        ]
    )]
    private function getFollowers(): void
    {
        $response = JsonBuilder::build();

        try {
            $followers = OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowerList();
            $users = [];

            foreach ($followers as $value) {
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

    #[Get('/user/following', name: 'followingList', auth: true)]
    #[OA\Get(
        path: '/user/following',
        summary: 'Get user following',
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Following List found'),
            new OA\Response(response: 403, description: 'User not logged'),
        ]
    )]
    private function getFollowing(): void
    {
        $response = JsonBuilder::build();

        try {
            $followers = OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowingList();
            $users = [];

            foreach ($followers as $value) {
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

    #[Get('/user/:userUUID/followers', variables: ["userUUID" => RouteParam::UUID], name: 'followerListByUUID', auth: true)]
    #[OA\Get(
        path: '/user/{userUUID}/followers',
        summary: 'Get user followers by UUID',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
            )],
        responses: [
            new OA\Response(response: 200, description: 'Follower List found'),
            new OA\Response(response: 403, description: 'User not logged'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function getFollowerListByUUID(string $userUUID): void
    {

        $response = JsonBuilder::build();

        try {
            $followers = OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowerList($userUUID);
            $users = [];

            foreach ($followers as $value) {
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

    #[Get('/user/:userUUID/following', variables: ["userUUID" => RouteParam::UUID], name: 'followingListByUUID', auth: true)]
    #[OA\Get(
        path: '/user/{userUUID}/following',
        summary: 'Get user followers by UUID',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
            )],
        responses: [
            new OA\Response(response: 200, description: 'Following List found'),
            new OA\Response(response: 403, description: 'User not logged'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function getFollowingListByUUID(string $userUUID): void
    {

        $response = JsonBuilder::build();

        try {
            $followers = OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowingList($userUUID);
            $users = [];

            foreach ($followers as $value) {
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

    #[Post('/user/:userUUID/follow', variables: ["userUUID" => RouteParam::UUID], name: 'followUser', auth: true)]
    #[OA\Post(
        path: '/user/{userUUID}/follow',
        summary: 'Follow user by UUID',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
            )],
        responses: [
            new OA\Response(response: 200, description: 'User followed'),
            new OA\Response(response: 403, description: 'User not logged'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function followUser(string $userUUID): void
    {
        $response = JsonBuilder::build();

        try {
            $followUser = OrmConnector::getInstance()->getRepository(FollowUser::class)->followUser($userUUID);
            $followUserData = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($followUser);


            foreach ($followUserData as $key => $value) {
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

    #[Delete('/user/:userUUID/unfollow', variables: ["userUUID" => RouteParam::UUID], name: 'unfollowUser', auth: true)]
    #[OA\Delete(
        path: '/user/{userUUID}/unfollow',
        summary: 'Unfollow user by UUID',
        tags: ['User'],
        parameters: [
            new OA\PathParameter(
                name: 'userUUID',
                description: 'User UUID',
            )],
        responses: [
            new OA\Response(response: 200, description: 'User unfollowed'),
            new OA\Response(response: 403, description: 'User not logged'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    private function unfollowUser(string $userUUID): void
    {
        $response = JsonBuilder::build();

        try {
            $followUser = OrmConnector::getInstance()->getRepository(FollowUser::class)->unfollowUser($userUUID);
            $followUserData = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($followUser);


            foreach ($followUserData as $key => $value) {
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