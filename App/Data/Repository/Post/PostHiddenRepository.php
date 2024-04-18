<?php

namespace Descolar\Data\Repository\Post;

use Descolar\App;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Post\PostHidden;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class PostHiddenRepository extends EntityRepository
{

    public function getAllHiddenPosts(): array
    {
        $userUUID = App::getUserUuid();
        if ($userUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $userUUID]);
        if ($user === null) {
            throw new EndpointException('User not logged', 403);
        }

        $posts = $this->createQueryBuilder('ph')
            ->select('ph')
            ->where('ph.isActive = 1')
            ->andWhere('ph.post.isActive = 1')
            ->andWhere('ph.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $postToReturn = [];
        foreach ($posts as $post) {
            $postToReturn[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post->getPost());
        }

        return $postToReturn;
    }

    public function hide(int $postId): array
    {

        if($postId < 1) {
            throw new EndpointException('Post not found', 404);
        }

        $post = OrmConnector::getInstance()->getRepository(Post::class)->findOneBy(['postId' => $postId]);
        if ($post === null) {
            throw new EndpointException('Post not found', 404);
        }

        if (App::getUserUuid() === null) {
            throw new EndpointException('User not logged', 403);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => App::getUserUuid()]);
        if ($user === null) {
            throw new EndpointException('User not found', 404);
        }

        $postHidden = $this->findOneBy(['post' => $post, 'user' => App::getUserUuid()]);

        if ($postHidden !== null) {
            throw new EndpointException('Post already hidden', 403);
        }

        $postHidden = new PostHidden();
        $postHidden->setPost($post);
        $postHidden->setUser($user);
        $postHidden->setIsActive(true);

        OrmConnector::getInstance()->persist($postHidden);
        OrmConnector::getInstance()->flush();

        return OrmConnector::getInstance()->getRepository(Post::class)->toJson($post);
    }

}