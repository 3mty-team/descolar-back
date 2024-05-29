<?php

namespace Descolar\Endpoints\Post;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;


use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Post\PostComment;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class PostCommentEndpoint extends AbstractEndpoint
{

    private function _getAllPostComments(int $postId, int $range, ?int $timestamp): void
    {

        $this->reply(function ($response) use ($postId, $range, $timestamp) {
            $group = OrmConnector::getInstance()->getRepository(PostComment::class)->toJsonRange($postId, $range, $timestamp);

            foreach ($group as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Get('/post/comment/:postId/:range', variables: ["postId" => RouteParam::NUMBER, "range" => RouteParam::NUMBER], name: 'getAllPostCommentInRange', auth: true)]
    private function getAllPostCommentInRange(int $postId, int $range): void
    {
        $this->_getAllPostComments($postId, $range, null);
    }

    #[Get('/post/comment/:postId/:range/:timestamp', variables: ["range" => RouteParam::NUMBER, "timestamp" => RouteParam::NUMBER], name: 'getAllPostCommentInRangeWithTimestamp', auth: true)]
    #[OA\Get(path: "/post/message/{postId}/{range}/{timestamp}", summary: "getAllPostCommentInRangeWithTimestamp", tags: ["Post"], parameters: [new OA\PathParameter("postId", "postId", "PostId"), new OA\PathParameter("range", "range", "Range", required: true), new OA\PathParameter("timestamp", "timestamp", "Timestamp", required: false)],
        responses: [new OA\Response(response: 200, description: "All posts comment retrieved")])]
    private function getAllPostCommentInRangeWithTimestamp(int $postId, int $range, int $timestamp): void
    {
        $this->_getAllPostComments($postId, $range, $timestamp);
    }

    #[Post('/post/:postId/comment', variables: ["postId" => RouteParam::NUMBER], name: 'createPostComment', auth: true)]
    #[OA\Post(path: "/post/{postId}/comment", summary: "createPostComment", tags: ["Post"], parameters: [new OA\PathParameter("postId", "postId", "PostId")],
        responses: [new OA\Response(response: 200, description: "Post comment created")])]
    private function createPostComment(int $postId): void
    {
        $this->reply(function ($response) use ($postId) {
            $content = $_POST['content'] ?? "";
            $date = $_POST['send_timestamp'] ?? 0;

            /** @var PostComment $post */
            $post = OrmConnector::getInstance()->getRepository(PostComment::class)->create($postId, $content, $date);
            $postData = OrmConnector::getInstance()->getRepository(PostComment::class)->toJson($post);

            foreach ($postData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/post/:commentId/comment', variables: ["commentId" => RouteParam::NUMBER], name: 'deletePostComment', auth: true)]
    #[OA\Delete(path: "/post/{commentId}/comment", summary: "deletePostComment", tags: ["Post"], parameters: [new OA\PathParameter("commentId", "commentId", "CommentId")],
        responses: [new OA\Response(response: 200, description: "Post comment deleted")])]
    private function deletePostComment(int $commentId): void
    {
        $this->reply(function ($response) use ($commentId) {
            $commentId = OrmConnector::getInstance()->getRepository(PostComment::class)->delete($commentId);

            $response->addData('comment', $commentId);
        });
    }
}