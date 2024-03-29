<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use OpenAPI\Attributes as OA;

class GroupMessageEndpoint extends AbstractEndpoint
{

    private function _getAllMessage(int $groupId, int $range, ?int $timestamp): void
    {

        $response = App::getJsonBuilder();

        try {

            $group = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->toJsonRange($groupId, $range, $timestamp);

            foreach ($group as $key => $value) {
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

    #[Get('/group/message/:groupId/:range', variables: ["groupId" => "[0-9]+", "range" => "[0-9]+", "timestamp" => "[0-9]+"], name: 'getAllGroupMessageInRange', auth: false)]
    #[OA\Get(path: "/group/message/{groupId}/{range}", summary: "getAllGroupMessageInRange", tags: ["Group"])]
    private function getAllGroupMessageInRange(int $groupId, int $range): void
    {
        $this->_getAllMessage($groupId, $range, null);
    }

    #[Get('/group/message/:groupId/:range/:timestamp', variables: ["groupId" => "[0-9]+", "range" => "[0-9]+", "timestamp" => "[0-9]+"], name: 'getAllGroupMessageInRangeWithTimestamp', auth: false)]
    #[OA\Get(path: "/group/message/{groupId}/{range}/{timestamp}", summary: "getAllGroupMessageInRangeWithTimestamp", tags: ["Group"])]
    private function getAllGroupMessageInRangeWithTimestamp(int $groupId, int $range, int $timestamp): void
    {
        $this->_getAllMessage($groupId, $range, $timestamp);
    }

    #[Post('/group/:groupId/message', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'createGroupMessage', auth: false)]
    #[OA\Post(path: "/group/{groupId}/message", summary: "createGroupMessage", tags: ["Group"])]
    private function createGroupMessage(int $groupId): void
    {
        $response = App::getJsonBuilder();

        try {

            $content = $_POST['content'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            /** @var GroupMessage $group */
            $group = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->create($groupId, $content, $date, $medias);
            $groupData = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->toJson($group);

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

    #[Put('/group/:groupId/:messageId/message', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'updateGroupMessage', auth: false)]
    #[OA\Put(path: "/group/{groupId}/{messageId}/message", summary: "updateGroupMessage", tags: ["Group"])]
    private function updateGroupMessage(int $groupId, int $messageId): void
    {
        global $_REQ;
        RequestUtils::cleanBody();
        $response = App::getJsonBuilder();

        try {

            $content = $_REQ['content'] ?? "";
            $medias = json_decode($_REQ['medias'] ?? '[]');

            /** @var GroupMessage $group */
            $group = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->update($groupId, $messageId, $content, $medias);
            $groupData = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->toJson($group);

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

    #[Delete('/group/:groupId/:messageId/message', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'deleteGroupMessage', auth: false)]
    #[OA\Delete(path: "/group/{groupId}/{messageId}/message", summary: "deleteGroupMessage", tags: ["Group"])]
    private function deleteGroupMessage(int $groupId, $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $group = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->delete($groupId, $messageId);

            $response->addData("id", $group);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

}