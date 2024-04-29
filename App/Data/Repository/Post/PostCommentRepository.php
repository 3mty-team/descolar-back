<?php

namespace Descolar\Data\Repository\Post;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostComment;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class PostCommentRepository extends EntityRepository
{

    public function findAllInRange(int $postId, int $range, ?int $timestamp): array
    {

        if ($range < 1) {
            throw new EndpointException('Range must be greater than 0', 400);
        }

        $post = OrmConnector::getInstance()->getRepository(Post::class)->findById($postId);

        $qb = $this->createQueryBuilder('pc')
            ->select('pc')
            ->where('pc.post = :post')
            ->andWhere('pc.isActive = 1')
            ->setParameter('post', $post)
            ->orderBy('pc.date', 'DESC')
            ->setMaxResults($range);

        if ($timestamp) {
            $date = new DateTime("@$timestamp", new DateTimeZone('Europe/Paris'));
            $qb->andWhere('pc.date > :timestamp')
                ->setParameter('timestamp', $date);

        }

        return $qb->getQuery()->getResult();
    }

    public function findById(int $id): PostComment
    {
        $postComment = $this->find($id);
        if ($postComment === null) {
            throw new EndpointException('Post comment not found', 404);
        }

        return $postComment;
    }

    public function create(int $postId, string $content, int $timestamp): PostComment
    {
        $post = OrmConnector::getInstance()->getRepository(Post::class)->findById($postId);
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $postComment = new PostComment();
        $postComment->setPost($post);
        $postComment->setUser($user);
        $postComment->setContent($content);
        $postComment->setDate(new DateTime("@$timestamp", new DateTimeZone('Europe/Paris')));
        $postComment->setIsActive(true);

        OrmConnector::getInstance()->persist($postComment);
        OrmConnector::getInstance()->flush();

        return $postComment;
    }

    public function delete(int $commentId): int
    {
        $comment = $this->findById($commentId);

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        if ($comment->getUser() !== $user) {
            throw new EndpointException('You are not the author of this comment', 403);
        }
        $comment->setIsActive(false);

        OrmConnector::getInstance()->persist($comment);
        OrmConnector::getInstance()->flush();

        return $comment->getId();
    }

    public function toJsonRange(int $postId, int $range, ?int $timestamp): array
    {

        $postsComment = $this->findAllInRange($postId, $range, $timestamp);

        $postCommentList = [];
        foreach ($postsComment as $postComment) {
            /** @var PostComment $postComment */
            $postCommentList[] = $this->toJson($postComment);
        }

        return $postCommentList;
    }

    public function toJson(PostComment $postComment): array
    {
        return [
            "id" => $postComment->getId(),
            "post" => OrmConnector::getInstance()->getRepository(Post::class)->toJson($postComment->getPost()),
            "user" => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($postComment->getUser()),
            "content" => $postComment->getContent(),
            "date" => $postComment->getDate(),
            "isActive" => $postComment->isActive()
        ];
    }

}