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
            $user = UserRepository::getLoggedUser();
            if ($user === null) {
                throw new EndpointException("User not logged", 403);
            }
        } else {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
        }

        if($user === null) {
            throw new EndpointException("User not found", 404);
        }

        return $user;
    }

    private function editFollowStatus(User $following, User $followed, bool $setFollowed) : FollowUser
    {
        $follow = $this->findOneBy(["following" => $following, "followed" => $followed]);
        if ($follow === null) {
            $followUser = new FollowUser();
            $followUser->setFollowing($following);
            $followUser->setFollower($followed);
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

    private function isFollow(User $following, User $followed): bool
    {
        $follow = $this->findOneBy(["following" => $following, "followed" => $followed]);
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

    public function followUser(string $userUUID): FollowUser
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $userToFollow = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
        if ($userToFollow === null) {
            throw new EndpointException("User not found", 404);
        }

        if ($user->getUUID() === $userToFollow->getUUID()) {
            throw new EndpointException("User cannot follow itself", 403);
        }

        if ($this->isFollow($user, $userToFollow)) {
            throw new EndpointException("User already blocked", 403);
        }

        return $this->editFollowStatus($user, $userToFollow, true);
    }

    public function unfollowUser(string $userUUID): FollowUser
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $userToUnfollow = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
        if ($userToUnfollow === null) {
            throw new EndpointException("User not found", 404);
        }

        if ($user->getUUID() === $userToUnfollow->getUUID()) {
            throw new EndpointException("User cannot unfollow itself", 403);
        }

        if (!$this->isFollow($user, $userToUnfollow)) {
            throw new EndpointException("User not followed", 403);
        }

        return $this->editFollowStatus($user, $userToUnfollow, false);
    }
}