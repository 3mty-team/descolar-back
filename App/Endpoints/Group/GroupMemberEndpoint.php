<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Group\GroupMember;
use Descolar\Managers\Endpoint\AbstractEndpoint;


use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use OpenAPI\Attributes as OA;

class GroupMemberEndpoint extends AbstractEndpoint
{

    #[Get('/group/:id/member', variables: ["id" => "[0-9]+"], name: 'getAllGroupMember', auth: false)]
    #[OA\Get(path: "/group/{id}/member", summary: "getAllGroupMember", tags: ["Group"])]
    private function getAllGroupMember(int $id): void
    {
        $response = App::getJsonBuilder();

        try {
            $userUUID = $_POST['userUUID'];
            $date = $_POST['date'];

            $groupMemberData = App::getOrmManager()->connect()->getRepository(GroupMember::class)->toJson($id);
            foreach ($groupMemberData as $key => $value) {
                $response->addData($key, $value);
            }

            $response = App::getJsonBuilder()->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Post('/group/:id/member', variables: ["id" => "[0-9]+"], name: 'addMemberInGroup', auth: false)]
    #[OA\Post(path: "/group/{id}/member", summary: "addMemberInGroup", tags: ["Group"])]
    private function addMemberInGroup(int $id): void
    {
        $response = App::getJsonBuilder();

        try {

            $userUUID = $_POST['user_uuid'] ?? "";
            $date = $_POST['date'] ?? "";

            $group = App::getOrmManager()->connect()->getRepository(GroupMember::class)->addMemberInGroup($id, $userUUID, $date);
            $groupData = App::getOrmManager()->connect()->getRepository(GroupMember::class)->toJson($group->getGroup()->getId());
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

    #[Delete('/group/:id/member', variables: ["id" => "[0-9]+"], name: 'removeMemberInGroup', auth: false)]
    #[OA\Delete(path: "/group/{id}/member", summary: "removeMemberInGroup", tags: ["Group"])]
    private function removeMemberInGroup(int $id): void {

        global $_REQ;
        RequestUtils::cleanBody();
        $response = App::getJsonBuilder();

        try {

            $userUUID = $_REQ['user_uuid'] ?? App::getUserUuid();

            $group = App::getOrmManager()->connect()->getRepository(GroupMember::class)->removeMemberInGroup($id, $userUUID);
            $groupData = App::getOrmManager()->connect()->getRepository(GroupMember::class)->toJson($group->getGroup()->getId());
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

}