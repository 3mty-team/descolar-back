<?php

namespace Descolar\Data\Repository\Post;

use DateTime;
use DateTimeZone;
use Descolar\App;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostComment;
use Descolar\Data\Entities\Post\PostLike;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{

    public function countReposts(Post $post): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.repostedPost = :post')
            ->andWhere('p.user != :postUser')
            ->andWhere('p.isActive = 1')
            ->setParameter('post', $post)
            ->setParameter('postUser', $post->getUser())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllInRange(int $range, ?int $timestamp): array
    {

        if ($range < 1) {
            throw new EndpointException('Range must be greater than 0', 400);
        }

        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.isActive = 1')
            ->orderBy('p.date', 'DESC')
            ->setMaxResults($range);

        if ($timestamp) {
            $date = new DateTime("@$timestamp", new DateTimeZone('Europe/Paris'));
            $qb->andWhere('p.date < :timestamp')
                ->setParameter('timestamp', $date);
        }

        return $qb->getQuery()->getResult();

    }

    public function findById(int $id): Post
    {
        $post = $this->find($id);

        if ($post === null) {
            throw new EndpointException("Post not found", 404);
        }

        return $post;
    }

    public function findAllInRangeByUser(string $userUUID, int $range, ?int $timestamp): array
    {

        if ($range < 1) {
            throw new EndpointException('Range must be greater than 0', 400);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
        if ($user === null) {
            throw new EndpointException('User not found', 404);
        }

        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.user = :user')
            ->andWhere('p.isActive = 1')
            ->setParameter('user', $user)
            ->orderBy('p.date', 'DESC')
            ->setMaxResults($range);

        if ($timestamp) {
            $date = new DateTime("@$timestamp");
            $qb->andWhere('p.date < :timestamp')
                ->setParameter('timestamp', $date);

        }

        return $qb->getQuery()->getResult();
    }

    public function findByContent(string $content, string $user_uuid): array
    {
        OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->addToSearchHistory($content, $user_uuid);

        return $this->createQueryBuilder('p')
            ->select('p')
            ->where("p.isActive = 1")
            ->andWhere("p.content LIKE '%$content%'")
            ->getQuery()
            ->getResult();
    }

    private function buildPost(?Post $retweetedPost, ?string $content, ?string $location, int $date, ?array $medias): Post
    {
        if (empty($content) || empty($date) || empty($location)) {
            throw new EndpointException('Missing parameters "content" or "location" or "date"', 400);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $medias ??= [];
        foreach ($medias as $media) {
            $media = OrmConnector::getInstance()->getRepository(Media::class)->find($media);
            if ($media === null) {
                throw new EndpointException('Media not found', 404);
            }
        }

        $post = new Post();
        $post->setUser($user);
        $post->setContent($content);
        $post->setLocation($location);
        $post->setDate(new DateTime("@$date"));
        $post->setIsActive(true);
        foreach ($medias as $media) {
            $post->addMedia(OrmConnector::getInstance()->getRepository(Media::class)->find($media));
        }

        if($retweetedPost) {
            $post->setRepostedPost($retweetedPost);
        }

        OrmConnector::getInstance()->persist($post);
        OrmConnector::getInstance()->flush();

        return $post;
    }

    public function create(?string $content, ?string $location, int $date, ?array $medias): Post
    {
        return $this->buildPost(null, $content, $location, $date, $medias);
    }

    public function repost(int $postId, ?string $content, ?string $location, int $date, ?array $medias): Post
    {
        $post = $this->find($postId);
        if($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        return $this->buildPost($post, $content, $location, $date, $medias);
    }

    public function delete(int $postId): int
    {
        $post = $this->find($postId);
        if($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        $post->setIsActive(false);
        OrmConnector::getInstance()->flush();

        return $postId;
    }

    private function buildPin(int $postId, bool $setToPin): Post
    {
        $post = $this->find($postId);
        if($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        /**
         * @var Post $post
         * @var User $user
         */

        if($post->getUser()->getUUID() !== $user->getUUID()) {
            throw new EndpointException('User not allowed to pin this post', 403);
        }

        $post->setPinned($setToPin);
        OrmConnector::getInstance()->flush();

        return $post;
    }

    public function getPinnedPost(string $userUUID): ?Post
    {
        if (empty($userUUID)) {
            throw new EndpointException('User not found', 403);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->find($userUUID);
        if ($user === null) {
            throw new EndpointException('User not found', 403);
        }

        /* @var ?Post $post */
        return $this->findOneBy(['user' => $user, 'isPinned' => true]);
    }

    public function pin(int $postId): ?Post
    {
        $userUUID = App::getUserUuid();

        if(($post = $this->getPinnedPost($userUUID)) !== null) {
            $post->setPinned(false);
            OrmConnector::getInstance()->flush();
        }

        return $this->buildPin($postId, true);
    }

    public function unpin(int $postId): Post
    {
        return $this->buildPin($postId, false);
    }

    public function toJsonRange(int $range, ?string $userUUID, ?int $timestamp): array
    {

        $posts = ($userUUID) ? $this->findAllInRangeByUser($userUUID, $range, $timestamp) : $this->findAllInRange($range, $timestamp);

        $postList = [];
        foreach ($posts as $post) {
            /** @var Post $post */
            $postList[] = $this->toJson($post);
        }

        return $postList;
    }

    public function toJson(Post $post): array
    {
        return [
            'id' => $post->getId(),
            'user' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($post->getUser()),
            'repostedPost' => ($post->getRepostedPost()) ? $this->toJson($post->getRepostedPost()) : null,
            'content' => $post->getContent(),
            'date' => $post->getDate(),
            'medias' => $post->getMedias()->map(fn($media) => $media->getId())->toArray(),
            'likes' => OrmConnector::getInstance()->getRepository(PostLike::class)->countLikes($post),
            'reposts' => $this->countReposts($post),
            'comments' => OrmConnector::getInstance()->getRepository(PostComment::class)->countComments($post), //NOT IMPLEMENTED
            'isActive' => $post->isActive(),
        ];
    }


}