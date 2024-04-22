<?php

namespace Descolar\Endpoints\Search;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;
use Descolar\App;

class SearchUserEndpoint extends AbstractEndpoint
{
    #[Get('/search/user/:username', variables: ["username" => RouteParam::STRING], name: 'searchUserByName', auth: true)]
    #[OA\Get(path: "/search/user/{username}", summary: "searchUserByName", tags: ["Search"], parameters: [new PathParameter("username", "username", "User Name", required: true)], responses: [new OA\Response(response: 200, description: "Users retrieved")])]
    private function searchUserByName(string $username): void
    {
        $response = JsonBuilder::build();
        $user_uuid = App::getUserUuid();

        try {
            /** @var User[] $users */
            $users = OrmConnector::getInstance()->getRepository(User::class)->findByUsername($username, $user_uuid);

            $data = [];
            foreach ($users as $user) {
                $data[] = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($user);
            }

            $response->setCode(200);
            $response->addData('users', $data);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}