<?php

namespace Descolar\Data\Repository\Post;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostLike;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class PostLikeRepository extends EntityRepository
{

    public function countLikes(Post $post): int
    {
        return $this->createQueryBuilder('pl')
            ->select('COUNT(pl.post)')
            ->where('pl.post = :post')
            ->andWhere('pl.isActive = 1')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLikedPosts(string $userUUID): array
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
        if ($user === null) {
            throw new EndpointException('User not found', 404);
        }

        $posts = $this->createQueryBuilder('pl')
            ->select('pl')
            ->where('pl.post.isActive = 1')
            ->andWhere('pl.isActive = 1')
            ->andWhere('pl.user = :userUUID')
            ->setParameter('userUUID', $userUUID)
            ->orderBy('pl.date', 'DESC')
            ->getQuery()
            ->getResult();

        $postToReturn = [];
        foreach ($posts as $post) {
            $postToReturn[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post->getPost());
        }

        return $postToReturn;
    }

    private function manageLikes(int $postId): array
    {
        if($postId < 1) {
            throw new EndpointException('Post not found', 404);
        }

        $post = OrmConnector::getInstance()->getRepository(Post::class)->findOneBy(['postId' => $postId]);
        if ($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        $userUUID = App::getUserUuid();

        if ($userUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
        if ($user === null) {
            throw new EndpointException('User not found', 404);
        }

        return [$post, $user];
    }

    public function like(int $postId): array
    {

        [$post, $user] = $this->manageLikes($postId);

        $postLike = $this->findOneBy(['post' => $post, 'user' => $user]);
        if ($postLike !== null) {
            throw new EndpointException('Post already liked', 400);
        }

        $postLike = new PostLike();
        $postLike->setPost($post);
        $postLike->setUser($user);
        $postLike->setIsActive(1);
        $postLike->setDate(new DateTime());

        OrmConnector::getInstance()->persist($postLike);
        OrmConnector::getInstance()->flush();

        return OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);
    }

    public function unlike(int $postId) {

        [$post, $user] = $this->manageLikes($postId);

        $postLike = $this->findOneBy(['post' => $post, 'user' => $user]);
        if ($postLike === null) {
            throw new EndpointException('Post not liked', 400);
        }

        $postLike->setIsActive(0);

        OrmConnector::getInstance()->persist($postLike);
        OrmConnector::getInstance()->flush();

        return OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);

    }


}