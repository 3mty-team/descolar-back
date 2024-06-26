<?php

namespace Descolar\Endpoints\Message;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\User\MessageUser;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;


class MessageEndpoint extends AbstractEndpoint
{

    private function _getAllMessages(int $range, ?string $userUUID = null, ?int $timestamp = null): void
    {
        $this->reply(function ($response) use ($range, $userUUID, $timestamp) {
            $group = OrmConnector::getInstance()->getRepository(MessageUser::class)->toJsonRange($range, $userUUID, $timestamp);

            foreach ($group as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Get('/message/conversation/:userUUID/:range', variables: ["userUUID" => RouteParam::UUID, "range" => RouteParam::NUMBER], name: 'getAllMessageUserInRange', auth: true)]
    private function getAllMessageUserInRange(string $userUUID, int $range): void
    {
        $this->_getAllMessages($range, $userUUID, null);
    }

    #[Get('/message/conversation/:userUUID/:range/:timestamp', variables: ["userUUID" => RouteParam::UUID, "range" => RouteParam::NUMBER, "timestamp" => RouteParam::TIMESTAMP], name: 'getAllMessageUserInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/message/conversation/{userUUID}/{range}/{timestamp}", summary: "getAllMessageUserInRangeWithTimestamp", tags: ["Message"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true), new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllMessageUserInRangeWithTimestamp(string $userUUID, int $range, int $timestamp): void
    {
        $this->_getAllMessages($range, $userUUID, $timestamp);
    }

    #[Post('/message', name: 'createMessage', auth: true)]
    #[OA\Post(path: "/message", summary: "createMessage", tags: ["Message"], responses: [new OA\Response(response: 200, description: "Message created")])]
    private function createMessage(): void
    {
        $this->reply(function ($response){
            [$receiver, $content, $date, $medias] = Requester::getInstance()->trackMany(
                "receiver_uuid", "content", "send_timestamp", "medias"
            );

            $medias = @json_decode($medias);

            $message = OrmConnector::getInstance()->getRepository(MessageUser::class)->create($receiver, $content, $date, $medias);
            $messageData = OrmConnector::getInstance()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Post('/message/:messageId/like', variables: ["messageId" => RouteParam::NUMBER], name: 'likeMessage', auth: true)]
    #[OA\Post(path: "/message/{messageId}/like", summary: "likeMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message liked")])]
    private function likeMessage(int $messageId): void
    {
        $this->reply(function ($response) use ($messageId){
            $message = OrmConnector::getInstance()->getRepository(MessageUser::class)->like($messageId);
            $messageData = OrmConnector::getInstance()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/message/:messageId/like', variables: ["messageId" => RouteParam::NUMBER], name: 'unlikeMessage', auth: true)]
    #[OA\Delete(path: "/message/{messageId}/like", summary: "unlikeMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message unliked")])]
    private function unlikeMessage(int $messageId): void
    {
        $this->reply(function ($response) use ($messageId){

            $message = OrmConnector::getInstance()->getRepository(MessageUser::class)->unlike($messageId);
            $messageData = OrmConnector::getInstance()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/message/:messageId/delete', variables: ["messageId" => RouteParam::NUMBER], name: 'deleteMessage', auth: false)]
    #[OA\Delete(path: "/message/{messageId}/delete", summary: "deleteMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message deleted")])]
    private function deleteMessage(int $messageId): void
    {
        $this->reply(function ($response) use ($messageId){
            $message = OrmConnector::getInstance()->getRepository(MessageUser::class)->delete($messageId);

            $response->addData("id", $message);
        });
    }
}