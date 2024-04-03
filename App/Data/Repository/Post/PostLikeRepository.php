<?php

namespace Descolar\Data\Repository\Post;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostLike;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class PostLikeRepository extends EntityRepository
{

    public function getLikedPosts(string $userUUID): array
    {
        $user = App::getOrmManager()->connect()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
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
            $postToReturn[] = App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post->getPost());
        }

        return $postToReturn;
    }

    private function manageLikes(int $postId): array
    {
        if($postId < 1) {
            throw new EndpointException('Post not found', 404);
        }

        $post = App::getOrmManager()->connect()->getRepository(Post::class)->findOneBy(['postId' => $postId]);
        if ($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        $userUUID = App::getUserUuid();

        if ($userUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $user = App::getOrmManager()->connect()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
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

        App::getOrmManager()->connect()->persist($postLike);
        App::getOrmManager()->connect()->flush();

        return App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);
    }

    public function unlike(int $postId) {

        [$post, $user] = $this->manageLikes($postId);

        $postLike = $this->findOneBy(['post' => $post, 'user' => $user]);
        if ($postLike === null) {
            throw new EndpointException('Post not liked', 400);
        }

        $postLike->setIsActive(0);

        App::getOrmManager()->connect()->persist($postLike);
        App::getOrmManager()->connect()->flush();

        return App::getOrmManager()->connect()->getRepository(Post::class)->toJson($post);

    }


}