<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Delete;

use Descolar\App;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;

use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostEndpoint extends AbstractEndpoint
{


    private function _getAllPosts(int $range, ?string $userUUID = null, ?int $timestamp = null): void
    {

        $response = App::getJsonBuilder();

        try {

            $group = App::getOrmManager()->connect()->getRepository(Post::class)->toJsonRange($range, $userUUID, $timestamp);

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

    #[Get('/post/message/:range', variables: ["range" => "[0-9]+"], name: 'getAllPostInRange', auth: true)]
    private function getAllPostInRange(int $range): void
    {
        $this->_getAllPosts($range, null, null);
    }

    #[Get('/post/message/:range/:timestamp', variables: ["range" => "[0-9]+", "timestamp" => "[0-9]+"], name: 'getAllPostInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/group/message/{range}/{timestamp}", summary: "getAllPostInRangeWithTimestamp", tags: ["Post"], parameters: [new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllPostInRangeWithTimestamp(int $range, int $timestamp): void
    {
        $this->_getAllPosts($range, timestamp: $timestamp);
    }

    #[Get('/post/message/:userUUID/:range', variables: ["userUUID" => ".?*", "range" => "[0-9]+"], name: 'getAllPostInRangeWithUserUUID', auth: true)]
    private function getAllPostInRangeWithUserUUID(string $userUUID, int $range): void
    {
        $this->_getAllPosts($range, userUUID: $userUUID);
    }

    #[Get('/post/message/:userUUID/:range/:timestamp', variables: ["userUUID" => ".?*", "range" => "[0-9]+", "timestamp" => "[0-9]+"], name: 'getAllPostInRangeWithUserUUIDAndTimestamp', auth: true)]
    #[OA\Get(path: "/group/message/{range}/{timestamp}", summary: "getAllPostInRangeWithUserUUIDAndTimestamp", tags: ["Post"], parameters: [new PathParameter("userUUID", "userUUID", "userUUID", required: true), new PathParameter("range", "range", "Range", required: true), new PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts retrieved")])]
    private function getAllPostInRangeWithUserUUIDAndTimestamp(string $userUUID, int $range, $timestamp): void
    {
        $this->_getAllPosts($range, $userUUID, $timestamp);
    }

    #[Get('/post/:postId', variables: ["postId" => "[0-9]+"], name: 'getPostById', auth: true)]
    #[OA\Get(path: "/post/{postId}", summary: "getPostById", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "Post ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Post retrieved")])]
    private function getPostById(int $postId): void
    {

        $response = App::getJsonBuilder();

        try {

            $post = App::getOrmManager()->connect()->getRepository(Post::class)->find($postId);
            $groupData = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

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
        $response = App::getJsonBuilder();

        try {

            $content = $_POST['content'] ?? "";
            $location = $_POST['location'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            /** @var Post $post */
            $post = App::getOrmManager()->connect()->getRepository(Post::class)->create($content, $location, $date, $medias);
            $postData = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

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
        $response = App::getJsonBuilder();

        try {

            $postId = $_POST['post_id'] ?? 0;
            $content = $_POST['content'] ?? "";
            $location = $_POST['location'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;
            $medias = @json_decode($_POST['medias'] ?? null);

            /** @var Post $post */
            $post = App::getOrmManager()->connect()->getRepository(Post::class)->repost($postId, $content, $location, $date, $medias);
            $postData = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

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

    #[Delete('/post/:postId', variables: ["postId" => "[0-9]+"], name: 'deletePost', auth: true)]
    #[OA\Delete(path: "/post/{postId}", summary: "deletePost", tags: ["Post"], parameters: [new PathParameter("postId", "postId", "Post ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Post deleted")])]
    private function deletePost(int $postId): void
    {
        $response = App::getJsonBuilder();

        try {
            $post = App::getOrmManager()->connect()->getRepository(Post::class)->delete($postId);

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