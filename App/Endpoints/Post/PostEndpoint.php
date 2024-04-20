<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Delete;

use Descolar\Data\Entities\Post\Post as PostEntity;

use Descolar\Adapters\Router\RouteParam;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;

use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostEndpoint extends AbstractEndpoint
{


    private function _getAllPosts(int $range, ?string $userUUID = null, ?int $timestamp = null): void
    {

        $response = JsonBuilder::build();

        try {

            $group = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJsonRange($range, $userUUID, $timestamp);

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

    #[Get('/post/message/:range', variables: ["range" => RouteParam::NUMBER], name: 'getAllPostInRange', auth: true)]
    private function getAllPostInRange(int $range): void
    {
        $this->_getAllPosts($range, null, null);
    }

    #[Get('/post/message/:range/:timestamp', variables: ["range" => RouteParam::NUMBER, "timestamp" => RouteParam::NUMBER], name: 'getAllPostInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/post/message/{range}/{timestamp}", summary: "getAllPostInRangeWithTimestamp", tags: ["Post"], parameters: [new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllPostInRangeWithTimestamp(int $range, int $timestamp): void
    {
        $this->_getAllPosts($range, timestamp: $timestamp);
    }

    #[Get('/post/message/:userUUID/:range', variables: ["userUUID" => RouteParam::UUID, "range" => RouteParam::NUMBER], name: 'getAllPostInRangeWithUserUUID', auth: true)]
    private function getAllPostInRangeWithUserUUID(string $userUUID, int $range): void
    {
        $this->_getAllPosts($range, userUUID: $userUUID);
    }

    #[Get('/post/message/:userUUID/:range/:timestamp', variables: ["userUUID" => RouteParam::UUID, "range" => RouteParam::NUMBER, "timestamp" => RouteParam::NUMBER], name: 'getAllPostInRangeWithUserUUIDAndTimestamp', auth: true)]
    #[OA\Get(path: "/post/message/{userUUID}/{range}/{timestamp}", summary: "getAllPostInRangeWithUserUUIDAndTimestamp", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true), new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllPostInRangeWithUserUUIDAndTimestamp(string $userUUID, int $range, $timestamp): void
    {
        $this->_getAllPosts($range, $userUUID, $timestamp);
    }

    #[Get('/post/:postId', variables: ["postId" => RouteParam::NUMBER], name: 'getPostById', auth: true)]
    #[OA\Get(path: "/post/{postId}", summary: "getPostById", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "Post ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Post retrieved")])]
    private function getPostById(int $postId): void
    {

        $response = JsonBuilder::build();

        try {

            $post = OrmConnector::getInstance()->getRepository(PostEntity::class)->find($postId);
            $groupData = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJson($post);

            foreach ($groupData as $key => $value) {
                $response->addData($key, $value);
            }

            $response->addData('post', $post->toJson());
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

    #[Post('/post', name: 'createPost', auth: true)]
    #[OA\Post(path: "/post", summary: "createPost", tags: ["Post"], responses: [new OA\Response(response: 200, description: "Post created")])]
    private function createPost() : void
    {
        $response = JsonBuilder::build();

        try {

            $content = $_POST['content'] ?? "";
            $location = $_POST['location'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            /** @var Post $post */
            $post = OrmConnector::getInstance()->getRepository(PostEntity::class)->create($content, $location, $date, $medias);
            $postData = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJson($post);

            foreach ($postData as $key => $value) {
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

    #[Post('/repost', name: 'repostPost', auth: true)]
    #[OA\Post(path: "/repost", summary: "repostPost", tags: ["Post"], responses: [new OA\Response(response: 200, description: "Post reposted")])]
    private function repostPost(): void
    {
        $response = JsonBuilder::build();

        try {

            $postId = $_POST['post_id'] ?? 0;
            $content = $_POST['content'] ?? "";
            $location = $_POST['location'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            /** @var Post $post */
            $post = OrmConnector::getInstance()->getRepository(PostEntity::class)->repost($postId, $content, $location, $date, $medias);
            $postData = OrmConnector::getInstance()->getRepository(PostEntity::class)->toJson($post);

            foreach ($postData as $key => $value) {
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

    #[Delete('/post/:postId', variables: ["postId" => RouteParam::NUMBER], name: 'deletePost', auth: true)]
    #[OA\Delete(path: "/post/{postId}", summary: "deletePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "Post ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Post deleted")])]
    private function deletePost(int $postId): void
    {
        $response = JsonBuilder::build();

        try {
            $post = OrmConnector::getInstance()->getRepository(PostEntity::class)->delete($postId);

            $response->addData("id", $post);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

}