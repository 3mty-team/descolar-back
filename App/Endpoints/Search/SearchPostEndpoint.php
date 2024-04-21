<?php

namespace Descolar\Endpoints\Search;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Post\Post;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class SearchPostEndpoint extends AbstractEndpoint
{
    #[Get('/search/post', name: 'searchUserByName', auth: true)]
    #[OA\Get(path: "/search/post", summary: "searchUserByName", tags: ["Search"], responses: [new OA\Response(response: 200, description: "Posts retrieved")])]
    private function searchPostByContent(): void
    {
        $response = JsonBuilder::build();
        $user_uuid = App::getUserUuid();

        try {
            global $_REQ;
            RequestUtils::cleanBody();
            $content = $_REQ['content'] ?? "";

            /** @var Post[] $posts */
            $posts = OrmConnector::getInstance()->getRepository(Post::class)->findByContent($content, $user_uuid);

            $data = [];
            foreach ($posts as $post) {
                $data[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);
            }

            $response->setCode(200);
            $response->addData('posts', $data);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}