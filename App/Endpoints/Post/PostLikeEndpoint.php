<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;

use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostHidden;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\App;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;

use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostLikeEndpoint extends AbstractEndpoint
{

    #[Get("post/:userUUID/like", variables: ["userUUID" => RouteParam::UUID], name: "likedPost", auth: true)]
    #[OA\Get(path: "/post/{userUUID}/like", summary: "likedPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)] ,responses: [new OA\Response(response: 200, description: "Post liked")])]
    private function likedPost(string $userUUID): void
    {
        $response = App::getJsonBuilder();

        try {

            $posts = App::getOrmManager()->connect()->getRepository(PostHidden::class)->getLikedPosts($userUUID);

            $response->addData('posts', $posts);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

    #[Put("post/:postId/like", variables: ["postId" => RouteParam::NUMBER] , name: "likePost", auth: true)]
    #[OA\Put(path: "/post/{postId}/like", summary: "likePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)] ,responses: [new OA\Response(response: 200, description: "Post liked")])]
    private function likePost(int $postId): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->like($postId);
            $postJson = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

            foreach ($postJson as $key => $value) {
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

    #[Delete("post/:postId/like", variables: ["postId" => RouteParam::NUMBER] , name: "unlikePost", auth: true)]
    #[OA\Delete(path: "/post/{postId}/like", summary: "unlikePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)] ,responses: [new OA\Response(response: 200, description: "Post unliked")])]
    private function unlikePost(int $postId): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->unlike($postId);
            $postJson = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

            foreach ($postJson as $key => $value) {
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