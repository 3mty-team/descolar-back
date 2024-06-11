<?php

namespace Descolar\Data\Repository\Post;

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
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $posts = $this->createQueryBuilder('ph')
            ->select('ph')
            ->where('ph.isActive = 1')
            ->andWhere('ph.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $postToReturn = [];
        foreach ($posts as $post) {
            /** @var PostHidden $post */
            if ($post->getPost()->isActive()) {
                $postToReturn[] = OrmConnector::getInstance()->getRepository(Post::class)->toJson($post->getPost());
            }
        }

        return $postToReturn;
    }

    private function manageHide(int $postId, bool $needToHide = true): PostHidden
    {

        $post = OrmConnector::getInstance()->getRepository(Post::class)->findById($postId);

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $postHidden = $this->findOneBy(['post' => $post, 'user' => $user]);

        if ($postHidden === null) {
            $postHidden = new PostHidden();
            $postHidden->setPost($post);
            $postHidden->setUser($user);
            $postHidden->setIsActive($needToHide);

            OrmConnector::getInstance()->persist($postHidden);
            OrmConnector::getInstance()->flush();

            return $postHidden;
        }

        if ($postHidden->isActive() === $needToHide) {
            $errorMessage = $needToHide ? 'Post already hidden' : 'Post already visible';
            throw new EndpointException($errorMessage, 403);
        }

        $postHidden->setIsActive($needToHide);
        OrmConnector::getInstance()->persist($postHidden);
        OrmConnector::getInstance()->flush();

        return $postHidden;
    }

    public function hide(int $postId): Post
    {
        $postHidden = $this->manageHide($postId);

        return $postHidden->getPost();
    }

    public function unHide(int $postId): Post
    {
        $postHidden = $this->manageHide($postId, false);

        return $postHidden->getPost();
    }

}