<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class GroupMessageEndpoint extends AbstractEndpoint
{

    private function _getAllMessage(int $groupId, int $range, ?int $timestamp): void
    {
        $this->reply(function ($response) use ($groupId, $range, $timestamp) {
            $group = OrmConnector::getInstance()->getRepository(GroupMessage::class)->toJsonRange($groupId, $range, $timestamp);

            foreach ($group as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Get('/group/message/:groupId/:range', variables: ["groupId" => RouteParam::NUMBER, "range" => RouteParam::NUMBER, "timestamp" => RouteParam::NUMBER], name: 'getAllGroupMessageInRange', auth: true)]
    #[OA\Get(path: "/group/message/{groupId}/{range}", summary: "getAllGroupMessageInRange", tags: ["Group"], parameters: [new PathParameter("groupId", "groupId", "Group ID", required: true), new PathParameter("range", "range", "Range", required: true)],
        responses: [new OA\Response(response: 200, description: "All group messages retrieved")])]
    private function getAllGroupMessageInRange(int $groupId, int $range): void
    {
        $this->_getAllMessage($groupId, $range, null);
    }

    #[Get('/group/message/:groupId/:range/:timestamp', variables: ["groupId" => RouteParam::NUMBER, "range" => RouteParam::NUMBER, "timestamp" => RouteParam::NUMBER], name: 'getAllGroupMessageInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/group/message/{groupId}/{range}/{timestamp}", summary: "getAllGroupMessageInRangeWithTimestamp", tags: ["Group"], parameters: [new PathParameter("groupId", "groupId", "Group ID", required: true), new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: true)],
        responses: [new OA\Response(response: 200, description: "All group messages retrieved")])]
    private function getAllGroupMessageInRangeWithTimestamp(int $groupId, int $range, int $timestamp): void
    {
        $this->_getAllMessage($groupId, $range, $timestamp);
    }

    #[Post('/group/:groupId/message', variables: ["groupId" => RouteParam::NUMBER, "messageId" => RouteParam::NUMBER], name: 'createGroupMessage', auth: true)]
    #[OA\Post(path: "/group/{groupId}/message", summary: "createGroupMessage", tags: ["Group"], parameters: [new PathParameter("groupId", "groupId", "Group ID", required: true)], responses: [new OA\Response(response: 200, description: "Group message created")])]
    private function createGroupMessage(int $groupId): void
    {
        $this->reply(function ($response) use ($groupId){
            [$content, $date, $medias] = Requester::getInstance()->trackMany(
                "content", "send_timestamp", "medias"
            );

            $medias = @json_decode($medias);

            /** @var GroupMessage $group */
            $group = OrmConnector::getInstance()->getRepository(GroupMessage::class)->create($groupId, $content, $date, $medias);
            $groupData = OrmConnector::getInstance()->getRepository(GroupMessage::class)->toJson($group);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Put('/group/:groupId/:messageId/message', variables: ["groupId" => RouteParam::NUMBER, "messageId" => RouteParam::NUMBER], name: 'updateGroupMessage', auth: true)]
    #[OA\Put(path: "/group/{groupId}/{messageId}/message", summary: "updateGroupMessage", tags: ["Group"], parameters: [new PathParameter("groupId", "groupId", "Group ID", required: true), new PathParameter("messageId", "messageId", "Message ID", required: true)], responses: [new OA\Response(response: 200, description: "Group message updated")])]
    private function updateGroupMessage(int $groupId, int $messageId): void
    {
        $this->reply(function ($response) use ($groupId, $messageId){
            [$content, $medias] = Requester::getInstance()->trackMany(
                "content", "medias"
            );

            $medias = @json_decode($medias);

            /** @var GroupMessage $group */
            $group = OrmConnector::getInstance()->getRepository(GroupMessage::class)->update($groupId, $messageId, $content, $medias);
            $groupData = OrmConnector::getInstance()->getRepository(GroupMessage::class)->toJson($group);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/group/:groupId/:messageId/message', variables: ["groupId" => RouteParam::NUMBER, "messageId" => RouteParam::NUMBER], name: 'deleteGroupMessage', auth: true)]
    #[OA\Delete(path: "/group/{groupId}/{messageId}/message", summary: "deleteGroupMessage", tags: ["Group"], parameters: [new PathParameter("groupId", "groupId", "Group ID", required: true), new PathParameter("messageId", "messageId", "Message ID", required: true)], responses: [new OA\Response(response: 200, description: "Group message deleted")])]
    private function deleteGroupMessage(int $groupId, $messageId): void
    {
        $this->reply(function ($response) use ($groupId, $messageId){
            $group = OrmConnector::getInstance()->getRepository(GroupMessage::class)->delete($groupId, $messageId);

            $response->addData("id", $group);
        });
    }

    #[Delete('/group/:messageId/message', variables: ["messageId" => RouteParam::NUMBER], name: 'deleteGroupMessageByMessageId', auth: true, moderationAuth: true)]
    #[OA\Delete(path: "/group/{messageId}/message", summary: "deleteGroupMessage", tags: ["Group"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)], responses: [new OA\Response(response: 200, description: "Group message deleted")])]
    private function deleteGroupMessageByMessageId(int $groupId, $messageId): void
    {
        $this->reply(function ($response) use ($groupId, $messageId){
            $group = OrmConnector::getInstance()->getRepository(GroupMessage::class)->deleteByMessageId($messageId);

            $response->addData("id", $group);
        });
    }
}