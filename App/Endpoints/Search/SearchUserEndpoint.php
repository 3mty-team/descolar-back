<?php

namespace Descolar\Endpoints\Search;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\App;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class SearchUserEndpoint extends AbstractEndpoint
{
    #[Get('/search/user/:username', variables: ["username" => RouteParam::STRING], name: 'searchUserByName', auth: true)]
    #[OA\Get(path: "/search/user/{username}", summary: "searchUserByName", tags: ["Search"], parameters: [new PathParameter("username", "username", "User Name", required: true)], responses: [new OA\Response(response: 200, description: "Users retrieved")])]
    private function searchUserByName(string $username): void
    {
        $this->reply(function ($response) use ($username) {

            $users = OrmConnector::getInstance()->getRepository(User::class)->findByUsername($username);

            $data = [];
            foreach ($users as $user) {
                $data[] = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($user);
            }

            $response->addData('users', $data);
        });
    }
}