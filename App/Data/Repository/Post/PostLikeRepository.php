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
            ->where('pl.isActive = 1')
            ->andWhere('pl.user = :user')
            ->setParameter('user', $user)
            ->orderBy('pl.date', 'DESC')
            ->getQuery()
            ->getResult();

        $postToReturn = [];
        foreach ($posts as $post) {
            /** @var PostLike $post */
            if ($post->getPost()->isActive()) {
                $postToReturn[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post->getPost());
            }
        }

        return $postToReturn;
    }

    private function manageLikes(int $postId): array
    {
        $post = OrmConnector::getInstance()->getRepository(Post::class)->findById($postId);

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        return [$post, $user];
    }

    public function like(int $postId): Post
    {

        [$post, $user] = $this->manageLikes($postId);

        $postLike = $this->findOneBy(['post' => $post, 'user' => $user]);
        if ($postLike !== null) {
            /** @var PostLike $postLike */
            if ($postLike->isActive()) {
                throw new EndpointException('Post already liked', 400);
            }

            $postLike->setIsActive(true);
            OrmConnector::getInstance()->flush();

            return $postLike->getPost();
        }

        $postLike = new PostLike();
        $postLike->setPost($post);
        $postLike->setUser($user);
        $postLike->setIsActive(true);
        $postLike->setDate(new DateTime());

        OrmConnector::getInstance()->persist($postLike);
        OrmConnector::getInstance()->flush();

        return $post;
    }

    public function unlike(int $postId): Post
    {

        [$post, $user] = $this->manageLikes($postId);

        $postLike = $this->findOneBy(['post' => $post, 'user' => $user]);
        if ($postLike === null || !$postLike->isActive()) {
            throw new EndpointException('Post not liked', 400);
        }

        $postLike->setIsActive(false);

        OrmConnector::getInstance()->persist($postLike);
        OrmConnector::getInstance()->flush();

        return $post;
    }


}