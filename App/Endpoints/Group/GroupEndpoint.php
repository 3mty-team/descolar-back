<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
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
        $response = App::getJsonBuilder();

        try {
            $group = App::getOrmManager()->connect()->getRepository(Group::class)->findById($id);
            $groupData = App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
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

    #[Post('/group', name: 'createGroup', auth: false)]
    #[OA\Post(path: "/group", summary: "createGroup", tags: ["Group"])]
    private function createGroup(): void
    {
        $response = App::getJsonBuilder();
        $name = $_POST['name'];
        $admin = $_POST['admin'];

        try {
            $group = App::getOrmManager()->connect()->getRepository(Group::class)->create($name, $admin);
            $groupData = App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
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

    #[Put('/group/:id', variables: ["id" => "[0-9]+"], name: 'updateGroup', auth: false)]
    #[OA\Put(path: "/group/{id}", summary: "updateGroup", tags: ["Group"])]
    private function updateGroup(int $id): void
    {
        global $_REQ;
        RequestUtils::cleanBody();
        $response = App::getJsonBuilder();
        $name = $_REQ['name'];
        $admin = $_REQ['admin'];

        try {
            $group = App::getOrmManager()->connect()->getRepository(Group::class)->editGroup($id, $name, $admin);
            $groupData = App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group);

            foreach ($groupData as $key => $value) {
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

    #[Delete('/group/:id', variables: ["id" => "[0-9]+"], name: 'deleteGroup', auth: false)]
    #[OA\Delete(path: "/group/{id}", summary: "deleteGroup", tags: ["Group"])]
    private function deleteGroup(int $id): void {

        $response = App::getJsonBuilder();

        try {

            $group = App::getOrmManager()->connect()->getRepository(Group::class)->deleteGroup($id);

            $response->addData('id', $group);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

}