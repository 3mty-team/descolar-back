<?php

namespace Descolar\Data\Repository\User;

use DateTimeZone;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class DeactivationUserRepository extends EntityRepository
{

    public function checkDeactivation(User $user): bool
    {
        /** @var DeactivationUser $deactivationUser */
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return false;
        }

        return $deactivationUser->isActive();
    }

    public function checkFinalDeactivation(User $user): bool
    {
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return false;
        }

        return $deactivationUser->isFinal();
    }

    private function manageDisable(?User $user = null, bool $forever = false)
    {

        if ($user === null) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        }

        $deactivationUser = new DeactivationUser();
        $deactivationUser->setUser($user);
        $deactivationUser->setDate(new \DateTime("now", new DateTimeZone('Europe/Paris')));
        $deactivationUser->setIsFinal($forever);
        $deactivationUser->setIsActive(true);

        $this->getEntityManager()->persist($deactivationUser);
        $this->getEntityManager()->flush();

        return $deactivationUser->getId();
    }

    public function disable(): int
    {
        return $this->manageDisable(null, false);
    }

    public function disableForever(User $user): int
    {
        return $this->manageDisable($user, true);
    }

    public function disableDeactivation(User $user): string
    {
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return "User is not deactivated.";
        }

        $deactivationUser->setIsActive(false);

        $this->getEntityManager()->persist($deactivationUser);
        $this->getEntityManager()->flush();

        return $user->getUUID();
    }
}