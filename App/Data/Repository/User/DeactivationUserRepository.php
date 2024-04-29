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

    public function checkDeactivation(User $user): bool {
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return false;
        }

        return $deactivationUser->getIsActive();
    }

    public function checkFinalDeactivation(User $user): bool {
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return false;
        }

        return $deactivationUser->getIsFinal();
    }

    private function manageDisable(bool $forever = false) {
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $deactivationUser = new DeactivationUser();
        $deactivationUser->setUser($user);
        $deactivationUser->setDate(new \DateTime("now", new DateTimeZone('Europe/Paris')));
        $deactivationUser->setIsFinal($forever);
        $deactivationUser->setIsActive(true);

        $this->getEntityManager()->persist($deactivationUser);
        $this->getEntityManager()->flush();

        return $deactivationUser->getId();
    }

    public function disable(): int {
        return $this->manageDisable();
    }

    public function disableForever(): int
    {
        return $this->manageDisable(true);
    }

    public function disableDeactivation(User $user): void {
        $deactivationUser = $this->findOneBy(["user" => $user]);
        if ($deactivationUser === null) {
            return;
        }

        if($deactivationUser->getIsFinal()) {
            throw new EndpointException("User is permanently disabled", 403);
        }

        $deactivationUser->setIsActive(false);

        $this->getEntityManager()->persist($deactivationUser);
        $this->getEntityManager()->flush();
    }
}