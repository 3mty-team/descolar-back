<?php

namespace Descolar\Endpoints\Message;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\App;
use Descolar\Data\Entities\User\MessageUser;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\Adapters\Router\Annotations\Get;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;


class MessageEndpoint extends AbstractEndpoint
{

    private function _getAllMessages(int $range, ?string $userUUID = null, ?int $timestamp = null): void
    {
        $response = App::getJsonBuilder();

        try {

            $group = App::getOrmManager()->connect()->getRepository(MessageUser::class)->toJsonRange($range, $userUUID, $timestamp);

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

    #[Get('/post/message/:userUUID/:range', variables: ["userUUID" => ".?*", "range" => "[0-9]+"], name: 'getAllMessageUserInRange', auth: true)]
    private function getAllMessageUserInRange(int $range): void
    {
        $this->_getAllMessages($range, null, null);
    }

    #[Get('/post/message/:userUUID/:range/:timestamp', variables: ["userUUID" => ".?*", "range" => "[0-9]+", "timestamp" => "[0-9]+"], name: 'getAllMessageUserInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/group/message/{userUUID}/{range}/{timestamp}", summary: "getAllMessageUserInRangeWithTimestamp", tags: ["Message"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true), new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllMessageUserInRangeWithTimestamp(int $range, int $timestamp): void
    {
        $this->_getAllMessages($range, timestamp: $timestamp);
    }

    #[Post('/post/message', name: 'createMessage', auth: true)]
    #[OA\Post(path: "/group/message", summary: "createMessage", tags: ["Message"], responses: [new OA\Response(response: 200, description: "Message created")])]
    private function createMessage(): void
    {

        $response = App::getJsonBuilder();

        try {

            $receiver = $_POST['receiver_uuid'] ?? '';
            $content = $_POST['content'] ?? '';
            $date = $_POST['date'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            $message = App::getOrmManager()->connect()->getRepository(MessageUser::class)->create($receiver, $content, $date, $medias);
            $messageData = App::getOrmManager()->connect()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
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

    #[Post('/post/message/:messageId/like', name: 'likeMessage', auth: true)]
    #[OA\Post(path: "/group/message/{messageId}/like", summary: "likeMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message liked")])]
    private function likeMessage(int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $message = App::getOrmManager()->connect()->getRepository(MessageUser::class)->like($messageId);
            $messageData = App::getOrmManager()->connect()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
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

    #[Delete('/post/message/:messageId/like', name: 'unlikeMessage', auth: true)]
    #[OA\Delete(path: "/group/message/{messageId}/like", summary: "unlikeMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message unliked")])]
    private function unlikeMessage(int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {

            $message = App::getOrmManager()->connect()->getRepository(MessageUser::class)->unlike($messageId);
            $messageData = App::getOrmManager()->connect()->getRepository(MessageUser::class)->toJson($message);

            foreach ($messageData as $key => $value) {
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

    #[Delete('/post/message/:messageId/delete', variables: ["messageId" => "[0-9]+"], name: 'deleteMessage', auth: true)]
    #[OA\Delete(path: "/group/message/{messageId}/delete", summary: "deleteMessage", tags: ["Message"], parameters: [new PathParameter("messageId", "messageId", "Message ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Message deleted")])]
    private function deleteMessage(int $messageId): void
    {
        $response = App::getJsonBuilder();

        try {
            $message = App::getOrmManager()->connect()->getRepository(MessageUser::class)->delete($messageId);

            $response->addData("id", $message);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}