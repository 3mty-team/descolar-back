<?php

namespace Descolar\Endpoints\Group;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\Group\GroupMessageLike;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use OpenAPI\Attributes as OA;

class GroupMessageLikeEndpoint extends AbstractEndpoint
{

    #[Get('/group/:groupId/:messageId/like', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'getAllGroupLike', auth: true)]
    #[OA\Get(path: "/group/{groupId}/{messageId}/like", summary: "getAllGroupLike", tags: ["Group"])]
    #[OA\Response(response: 200, description: "All group messages like retrieved")]
    private function getAllGroupMessageLike(int $groupId, int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $groupLikeData = App::getOrmManager()->connect()->getRepository(GroupMessageLike::class)->toJson($groupId, $messageId);
            foreach ($groupLikeData as $key => $value) {
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

    #[Post('/group/:groupId/:messageId/like', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'likeGroupMessage', auth: true)]
    #[OA\Post(path: "/group/{groupId}/{messageId}/like", summary: "likeGroupMessage", tags: ["Group"])]
    #[OA\Response(response: 200, description: "Group message liked")]
    private function likeGroupMessage(int $groupId, int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $groupMessageLike = App::getOrmManager()->connect()->getRepository(GroupMessageLike::class)->like($groupId, $messageId);
            $groupLikeData = App::getOrmManager()->connect()->getRepository(GroupMessageLike::class)->toJson($groupMessageLike->getGroupMessage()->getGroup()->getId(), $groupMessageLike->getGroupMessage()->getId());

            foreach ($groupLikeData as $key => $value) {
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

    #[Delete('/group/:groupId/:messageId/like', variables: ["groupId" => "[0-9]+", "messageId" => "[0-9]+"], name: 'unlikeGroupMessage', auth: true)]
    #[OA\Delete(path: "/group/{groupId}/{messageId}/like", summary: "unlikeGroupMessage", tags: ["Group"])]
    #[OA\Response(response: 200, description: "Group message unliked")]
    private function unlikeGroupMessage(int $groupId, int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $groupMessageLike = App::getOrmManager()->connect()->getRepository(GroupMessageLike::class)->unlike($groupId, $messageId);

            $response->addData('id', $groupMessageLike);

            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }


}