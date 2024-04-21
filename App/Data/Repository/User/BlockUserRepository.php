<?php

namespace Descolar\Data\Repository\User;

use Descolar\Data\Entities\User\BlockUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class BlockUserRepository extends EntityRepository
{

    /**
     * @return User[] blocked by the user
     */
    private function getBlocking(User $user): array
    {
        $query = $this->createQueryBuilder("b")
            ->where("b.blocking = :user")
            ->setParameter("user", $user)
            ->getQuery()->getResult();

        $users = [];
        foreach ($query as $block) {
            $users[] = $block->getBlocked();
        }

        return $users;
    }

    /**
     * @return User[] blocking the user
     */
    private function getBlocked(User $user): array
    {
        $query = $this->createQueryBuilder("b")
            ->where("b.blocked = :user")
            ->setParameter("user", $user)
            ->getQuery()->getResult();

        $users = [];
        foreach ($query as $block) {
            $users[] = $block->getBlocking();
        }

        return $users;
    }

    private function editBlockStatus(User $blocking, User $blocked, bool $setBlocked) : BlockUser
    {
        $block = $this->findOneBy(["blocking" => $blocking, "blocked" => $blocked]);
        if ($block === null) {
            $blockUser = new BlockUser();
            $blockUser->setBlocking($blocking);
            $blockUser->setBlocked($blocked);
            $blockUser->setIsActive($setBlocked);

            $this->getEntityManager()->persist($blockUser);
            $this->getEntityManager()->flush();
            return $blockUser;
        }

        $block->setIsActive($setBlocked);

        $this->getEntityManager()->persist($block);
        $this->getEntityManager()->flush();

        return $block;
    }

    /**
     * @param User $blocking the user who blocks
     * @param User $blocked the user who is blocked to check
     * @return bool if the user is blocked return true, otherwise false
     */
    private function isBlocked(User $blocking, User $blocked): bool
    {
        $blocked = $this->findOneBy(["blocking" => $blocking, "blocked" => $blocked]);
        if ($blocked !== null) {
            return $blocked->isActive();
        }

        return false;
    }

    /**
     * @return User[]
     */
    public function getBlockList(): array
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        return $this->getBlocking($user);
    }

    public function checkBlockedStatus(string $userUUID): bool
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $userToCheck = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        if ($userToCheck === null) {
            throw new EndpointException("User not found", 404);
        }

        return $this->isBlocked($user, $userToCheck);
    }

    public function blockUser(string $userUUID): BlockUser
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $userToBlock = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        if ($userToBlock === null) {
            throw new EndpointException("User not found", 404);
        }

        if ($user->getUUID() === $userToBlock->getUUID()) {
            throw new EndpointException("User cannot block itself", 403);
        }

        if ($this->isBlocked($user, $userToBlock)) {
            throw new EndpointException("User already blocked", 403);
        }

        return $this->editBlockStatus($user, $userToBlock, true);
    }

    public function unBlockUser(string $userUUID): BlockUser
    {
        $user = UserRepository::getLoggedUser();
        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $userToUnBlock = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);
        if ($userToUnBlock === null) {
            throw new EndpointException("User not found", 404);
        }

        if ($user->getUUID() === $userToUnBlock->getUUID()) {
            throw new EndpointException("User cannot unblock itself", 403);
        }

        if (!$this->isBlocked($user, $userToUnBlock)) {
            throw new EndpointException("User not blocked", 403);
        }

        return $this->editBlockStatus($user, $userToUnBlock, false);
    }

    public function toJson(BlockUser $block): array
    {
        return [
            'blocking' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($block->getBlocking()),
            'blocked' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($block->getBlocked()),
        ];
    }

}