<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Get;

use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\PostHidden;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\App;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;

use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostHiddenEndpoint extends AbstractEndpoint
{

    #[Get("post/hide", name: "hiddenPost", auth: true)]
    #[OA\Get(path: "/post/hide", summary: "hiddenPost", tags: ["Post"], responses: [new OA\Response(response: 200, description: "Post hidden")])]
    private function hiddenPost(): void
    {
        $response = App::getJsonBuilder();

        try {

            $posts = App::getOrmManager()->connect()->getRepository(PostHidden::class)->getAllHiddenPosts();

            $response->addData('posts', $posts);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

    #[Put("post/:postId/hide", variables: ["postId" => RouteParam::NUMBER] , name: "hidePost", auth: true)]
    #[OA\Put(path: "/post/{postId}/hide", summary: "hidePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "postId", required: true)] ,responses: [new OA\Response(response: 200, description: "Post hidden")])]
    private function hidePost(string $postId): void
    {
        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(PostHidden::class)->hide($postId);

            foreach ($post as $key => $value) {
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