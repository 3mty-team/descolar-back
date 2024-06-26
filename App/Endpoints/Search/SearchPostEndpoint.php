<?php

namespace Descolar\Endpoints\Search;

use Descolar\Adapters\Router\Annotations\Post as PostAttribute;
use Descolar\Data\Entities\Post\Post;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;

class SearchPostEndpoint extends AbstractEndpoint
{
    #[PostAttribute('/search/post', name: 'searchUserByName', auth: true)]
    #[OA\Post(path: "/search/post", summary: "searchUserByName", tags: ["Search"], responses: [new OA\Response(response: 200, description: "Posts retrieved")])]
    private function searchPostByContent(): void
    {
        $this->reply(function ($response) {
            $content = Requester::getInstance()->trackOne("content");

            /** @var Post[] $posts */
            $posts = OrmConnector::getInstance()->getRepository(Post::class)->findByContent($content);

            $data = [];
            foreach ($posts as $post) {
                $data[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);
            }

            $response->addData('posts', $data);
        });
    }
}