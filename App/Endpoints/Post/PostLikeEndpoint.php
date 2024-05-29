<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\Post as PostEntity;
use Descolar\Data\Entities\Post\PostLike;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostLikeEndpoint extends AbstractEndpoint
{

    #[Get("post/:userUUID/like", variables: ["userUUID" => RouteParam::UUID], name: "likedPost", auth: true)]
    #[OA\Get(path: "/post/{userUUID}/like", summary: "likedPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)], responses: [new OA\Response(response: 200, description: "Post liked")])]
    private function likedPost(string $userUUID): void
    {
        $this->reply(function ($response) use ($userUUID) {
            $posts = OrmConnector::getInstance()->getRepository(PostLike::class)->getLikedPosts($userUUID);

            $response->addData('posts', $posts);
        });
    }

    #[Post("post/:postId/like", variables: ["postId" => RouteParam::NUMBER], name: "likePost", auth: true)]
    #[OA\Post(path: "/post/{postId}/like", summary: "likePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)], responses: [new OA\Response(response: 200, description: "Post liked")])]
    private function likePost(int $postId): void
    {
        $this->reply(function ($response) use ($postId) {
            $post = OrmConnector::getInstance()->getRepository(PostLike::class)->like($postId);
            $postJson = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJson($post);

            foreach ($postJson as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete("post/:postId/like", variables: ["postId" => RouteParam::NUMBER], name: "unlikePost", auth: true)]
    #[OA\Delete(path: "/post/{postId}/like", summary: "unlikePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)], responses: [new OA\Response(response: 200, description: "Post unliked")])]
    private function unlikePost(int $postId): void
    {
        $this->reply(function ($response) use ($postId) {
            $post = OrmConnector::getInstance()->getRepository(PostLike::class)->unlike($postId);
            $postJson = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJson($post);

            foreach ($postJson as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}