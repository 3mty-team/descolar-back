<?php

namespace Descolar\Data\Repository\User;

use Descolar\Data\Entities\User\FollowUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class FollowUserRepository extends EntityRepository
{
    /**
     * @return User[] following by the user
     */
    private function getFollowing(User $user): array
    {
        $query = $this->createQueryBuilder("f")
            ->where("f.following = :user")
            ->setParameter("user", $user)
            ->getQuery()->getResult();

        $users = [];
        foreach ($query as $follow) {
            /** @var FollowUser $follow */
            $users[] = $follow->getFollowing();
        }

        return $users;
    }

    /**
     * @return User[] followed the user
     */
    private function getFollowed(User $user): array
    {
        $query = $this->createQueryBuilder("f")
            ->where("f.follower = :user")
            ->setParameter("user", $user)
            ->getQuery()->getResult();

        $users = [];
        foreach ($query as $follow) {
            /** @var FollowUser $follow */
            $users[] = $follow->getFollower();
        }

        return $users;
    }

    private function getUserFromUUID(?string $userUUID): User
    {
        $user = null;

        if($userUUID === null) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        } else {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
        }

        return $user;
    }

    private function editFollowStatus(User $following, User $follower, bool $setFollowed) : FollowUser
    {
        $follow = $this->findOneBy(["following" => $following, "follower" => $follower]);
        if ($follow === null) {
            $followUser = new FollowUser();
            $followUser->setFollowing($following);
            $followUser->setFollower($follower);
            $followUser->setDate(new \DateTime("now", new \DateTimeZone('Europe/Paris')));
            $followUser->setIsActive($setFollowed);

            $this->getEntityManager()->persist($followUser);
            $this->getEntityManager()->flush();
            return $followUser;
        }

        $follow->setIsActive($setFollowed);

        $this->getEntityManager()->persist($follow);
        $this->getEntityManager()->flush();

        return $follow;
    }

    private function isFollow(User $following, User $follower): bool
    {
        $follow = $this->findOneBy(["following" => $following, "follower" => $follower]);
        if ($follow !== null) {
            return $follow->isActive();
        }

        return false;
    }

    public function getFollowerList(?string $userUUID = null): array
    {
        $user = $this->getUserFromUUID($userUUID);

        return $this->getFollowing($user);
    }

    public function getFollowingList(?string $userUUID = null): array
    {
        $user = $this->getUserFromUUID($userUUID);

        return $this->getFollowing($user);
    }

    public function followUser(string $userUUID): User
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $userToFollow = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        if ($user->getUUID() === $userToFollow->getUUID()) {
            throw new EndpointException("User cannot follow itself", 403);
        }

        if ($this->isFollow($user, $userToFollow)) {
            throw new EndpointException("User already followed", 403);
        }

        return $this->editFollowStatus($user, $userToFollow, true)->getFollowing();
    }

    public function unfollowUser(string $userUUID): User
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $userToUnfollow = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        if ($user->getUUID() === $userToUnfollow->getUUID()) {
            throw new EndpointException("User cannot unfollow itself", 403);
        }

        if (!$this->isFollow($user, $userToUnfollow)) {
            throw new EndpointException("User not followed", 403);
        }

        return $this->editFollowStatus($user, $userToUnfollow, false)->getFollowing();
    }
}