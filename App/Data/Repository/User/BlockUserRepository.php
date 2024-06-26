<?php

namespace Descolar\Data\Repository\User;

use Descolar\Data\Entities\User\BlockUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
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
            ->andWhere("b.isActive = true")
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
            ->andWhere("b.isActive = true")
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
            $blockUser->setDate(new \DateTime("now", new \DateTimeZone('Europe/Paris')));
            $blockUser->setIsActive($setBlocked);

            Validator::getInstance($blockUser)->check();

            OrmConnector::getInstance()->persist($blockUser);
            OrmConnector::getInstance()->flush();
            return $blockUser;
        }

        $block->setDate(new \DateTime("now", new \DateTimeZone('Europe/Paris')));
        $block->setIsActive($setBlocked);

        Validator::getInstance($block)->check();

        OrmConnector::getInstance()->persist($block);
        OrmConnector::getInstance()->flush();

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

        return $blocked?->isActive() ?? false;
    }

    /**
     * @return User[]
     */
    public function getBlockList(): array
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        return $this->getBlocking($user);
    }

    public function checkBlockedStatus(string $userUUID): bool
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $userToCheck = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        return $this->isBlocked($user, $userToCheck);
    }

    public function blockUser(string $userUUID): BlockUser
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $userToBlock = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

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
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $userToUnBlock = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

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