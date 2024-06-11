<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\Post;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostPinEndpoint extends AbstractEndpoint
{

    #[Get("post/:userUUID/pin", variables: ["userUUID" => RouteParam::UUID], name: "pinnedPost", auth: true)]
    #[OA\Get(path: "/post/{userUUID}/pin", summary: "pinnedPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)], responses: [new OA\Response(response: 200, description: "Post pinned")])]
    private function pinnedPost(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $post = OrmConnector::getInstance()->getRepository(Post::class)->getPinnedPost($userUUID);

            if ($post) {
                $postJson = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

                foreach ($postJson as $key => $value) {
                    $response->addData($key, $value);
                }
            }
        });
    }

    #[Put("post/:postId/pin", variables: ["postId" => RouteParam::NUMBER], name: "pinPost", auth: true)]
    #[OA\Put(path: "/post/{postId}/pin", summary: "pinPost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)], responses: [new OA\Response(response: 200, description: "Post pinned")])]
    private function pinPost(int $postId): void
    {
        $this->reply(function ($response) use ($postId) {
            $post = OrmConnector::getInstance()->getRepository(Post::class)->pin($postId);
            $postJson = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

            foreach ($postJson as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete("post/:userUUID/pin", variables: ["postId" => RouteParam::UUID], name: "unpinPost", auth: true)]
    #[OA\Delete(path: "/post/{userUUID}/pin", summary: "unpinPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)], responses: [new OA\Response(response: 200, description: "Post unpinned")])]
    private function unpinPost(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $post = OrmConnector::getInstance()->getRepository(Post::class)->unpin($userUUID);
            $postJson = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

            foreach ($postJson as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}