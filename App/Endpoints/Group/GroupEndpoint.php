<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use OpenAPI\Attributes as OA;

class GroupEndpoint extends AbstractEndpoint
{

    #[Get('/group', name: 'getAllGroups', auth: false)]
    #[OA\Get(path: "/group", summary: "getAllGroups", tags: ["Group"])]
    private function getAllGroups(): void
    {

        /** @var Group[] $groups */
        $groups = App::getOrmManager()->connect()->getRepository(Group::class)->findAll();

        $data = [];

        foreach ($groups as $group) {
            $data[] = App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group);
        }

        $response = App::getJsonBuilder()->setCode(200);
        $response->addData('groups', $data);

        $response->getResult();
    }

    #[Get('/group/:id', variables: ["id" => "[0-9]+"], name: 'getGroupById', auth: false)]
    #[OA\Get(path: "/group/{id}", summary: "getGroupById", tags: ["Group"])]
    private function getGroupById(int $id): void
    {

        $group = App::getOrmManager()->connect()->getRepository(Group::class)->find($id);


        if ($group === null) {
            $response = App::getJsonBuilder()->setCode(404);
            $response->addData('message', 'Group not found');
            $response->getResult();
            return;
        }

        $response = App::getJsonBuilder()->setCode(200);
        $response->addData('group', App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group));
        $response->getResult();
    }

    #[Post('/group', name: 'createGroup', auth: false)]
    #[OA\Post(path: "/group", summary: "createGroup", tags: ["Group"])]
    private function createGroup(): void
    {

        $name = $_POST['name'];
        $adminId = $_POST['adminId'];

        App::getOrmManager()->connect()->getRepository(Group::class)->create($name, $adminId);

    }

}