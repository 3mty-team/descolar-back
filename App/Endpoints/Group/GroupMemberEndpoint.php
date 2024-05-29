<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Group\GroupMember;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;


class GroupMemberEndpoint extends AbstractEndpoint
{

    #[Get('/group/:id/member', variables: ["id" => RouteParam::NUMBER], name: 'getAllGroupMember', auth: true)]
    #[OA\Get(path: "/group/{id}/member", summary: "getAllGroupMember", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)],
        responses: [new OA\Response(response: 200, description: "All group members retrieved")]
    )]
    private function getAllGroupMember(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            $userUUID = $_POST['userUUID'];
            $date = $_POST['date'];

            $groupMemberData = OrmConnector::getInstance()->getRepository(GroupMember::class)->toJson($id);
            foreach ($groupMemberData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Post('/group/:id/member', variables: ["id" => RouteParam::NUMBER], name: 'addMemberInGroup', auth: true)]
    #[OA\Post(path: "/group/{id}/member", summary: "addMemberInGroup", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Member added")])]
    private function addMemberInGroup(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            $userUUID = $_POST['user_uuid'] ?? "";
            $date = $_POST['date'] ?? "";

            $group = OrmConnector::getInstance()->getRepository(GroupMember::class)->addMemberInGroup($id, $userUUID, $date);
            $groupData = OrmConnector::getInstance()->getRepository(GroupMember::class)->toJson($group->getGroup()->getId());
            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/group/:id/member', variables: ["id" => RouteParam::NUMBER], name: 'removeMemberInGroup', auth: true)]
    #[OA\Delete(path: "/group/{id}/member", summary: "removeMemberInGroup", tags: ["Group"], parameters: [new PathParameter("id", "id", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Member removed")])]
    private function removeMemberInGroup(int $id): void
    {
        $this->reply(function ($response) use ($id) {
            global $_REQ;
            RequestUtils::cleanBody();

            $userUUID = $_REQ['user_uuid'] ?? App::getUserUuid();

            $group = OrmConnector::getInstance()->getRepository(GroupMember::class)->removeMemberInGroup($id, $userUUID);
            $groupData = OrmConnector::getInstance()->getRepository(GroupMember::class)->toJson($group->getGroup()->getId());
            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}