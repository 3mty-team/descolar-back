<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\Data\Entities\Group\Group;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Response;

class GroupEndpoint extends AbstractEndpoint
{

    #[Get('/group', name: 'getAllGroups', auth: true)]
    #[OA\Get(path: "/group", summary: "getAllGroups", tags: ["Group"], responses: [new OA\Response(response: 200, description: "All groups retrieved")])]
    private function getAllGroups(): void
    {
        $this->reply(function ($response) {
            /** @var Group[] $groups */
            $groups = OrmConnector::getInstance()->getRepository(Group::class)->findAll();

            $data = [];
            foreach ($groups as $group) {
                $data[] = OrmConnector::getInstance()->getRepository(Group::class)->toJson($group);
                $response->addData($data["name"], $data);
            }
        });
    }

    #[Get('/group/:id', variables: ["id" => RouteParam::NUMBER], name: 'getGroupById', auth: true)]
    #[OA\Get(path: "/group/{id}", summary: "getGroupById", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Group retrieved")])]
    private function getGroupById(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($id);
            $groupData = OrmConnector::getInstance()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Post('/group', name: 'createGroup', auth: true)]
    #[OA\Post(path: "/group", summary: "createGroup", tags: ["Group"], responses: [new Response(response: 200, description: "Group created")])]
    private function createGroup(): void
    {
        $this->reply(function ($response) {
            [$name, $admin] = Requester::getInstance()->trackMany(
                "name", "admin"
            );

            $group = OrmConnector::getInstance()->getRepository(Group::class)->create($name, $admin);
            $groupData = OrmConnector::getInstance()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Put('/group/:id', variables: ["id" => RouteParam::NUMBER], name: 'updateGroup', auth: true)]
    #[OA\Put(path: "/group/{id}", summary: "updateGroup", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Group created")])]
    private function updateGroup(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            [$name, $admin] = Requester::getInstance()->trackMany(
                "name", "admin"
            );

            $group = OrmConnector::getInstance()->getRepository(Group::class)->editGroup($id, $name, $admin);
            $groupData = OrmConnector::getInstance()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/group/:id', variables: ["id" => RouteParam::NUMBER], name: 'deleteGroup', auth: true)]
    #[OA\Delete(path: "/group/{id}", summary: "deleteGroup", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Group deleted")])]
    private function deleteGroup(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            $group = OrmConnector::getInstance()->getRepository(Group::class)->deleteGroup($id);

            $response->addData('id', $group);
        });
    }
}