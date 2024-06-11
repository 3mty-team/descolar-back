<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostHidden;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostHiddenEndpoint extends AbstractEndpoint
{
    #[Get("post/hide", name: "hiddenPost", auth: true)]
    #[OA\Get(path: "/post/hide", summary: "hiddenPost", tags: ["Post"], responses: [new OA\Response(response: 200, description: "Post hidden")])]
    private function hiddenPost(): void
    {
        $this->reply(function ($response){
            $posts = OrmConnector::getInstance()->getRepository(PostHidden::class)->getAllHiddenPosts();

            $response->addData('posts', $posts);
        });
    }

    #[Put("post/:postId/hide", variables: ["postId" => RouteParam::NUMBER] , name: "hidePost", auth: true)]
    #[OA\Put(path: "/post/{postId}/hide", summary: "hidePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)] ,responses: [new OA\Response(response: 200, description: "Post hidden")])]
    private function hidePost(string $postId): void
    {
        $this->reply(function ($response) use ($postId){
            $post = OrmConnector::getInstance()->getRepository(PostHidden::class)->hide($postId);
            $postData = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

            foreach ($postData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete("post/:postId/hide", variables: ["postId" => RouteParam::NUMBER] , name: "unHidePost", auth: true)]
    #[OA\Delete(path: "/post/{postId}/hide", summary: "unHidePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)] ,responses: [new OA\Response(response: 200, description: "Post unhidden")])]
    private function unHidePost(string $postId): void
    {
        $this->reply(function ($response) use ($postId){
            $post = OrmConnector::getInstance()->getRepository(PostHidden::class)->unHide($postId);
            $postData = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

            foreach ($postData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}