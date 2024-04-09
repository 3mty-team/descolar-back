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

class PostPinEndpoint extends AbstractEndpoint
{

    #[Get("post/:userUUID/pin", variables: ["userUUID" => RouteParam::UUID], name: "pinnedPost", auth: true)]
    #[OA\Get(path: "/post/{userUUID}/pin", summary: "pinnedPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)], responses: [new OA\Response(response: 200, description: "Post pinned")])]
    private function pinnedPost(string $userUUID): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->getPinnedPost($userUUID);
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

    #[Put("post/:postId/pin", variables: ["postId" => RouteParam::NUMBER], name: "pinPost", auth: true)]
    #[OA\Put(path: "/post/{postId}/pin", summary: "pinPost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)], responses: [new OA\Response(response: 200, description: "Post pinned")])]
    private function pinPost(int $postId): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->pin($postId);
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

    #[Delete("post/:userUUID/pin", variables: ["postId" => RouteParam::UUID], name: "unpinPost", auth: true)]
    #[OA\Delete(path: "/post/{userUUID}/pin", summary: "unpinPost", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true)], responses: [new OA\Response(response: 200, description: "Post unpinned")])]
    private function unpinPost(string $userUUID): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->unpin($userUUID);
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