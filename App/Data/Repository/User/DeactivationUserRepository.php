<?php

namespace Descolar\Data\Repository\User;

use DateTimeZone;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
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

    public function disable(): int {
        $user = UserRepository::getLoggedUser();

        if ($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $deactivationUser = new DeactivationUser();
        $deactivationUser->setUser($user);
        $deactivationUser->setDate(new \DateTime("now", new DateTimeZone('Europe/Paris')));
        $deactivationUser->setIsFinal(false);
        $deactivationUser->setIsActive(true);

        $this->getEntityManager()->persist($deactivationUser);
        $this->getEntityManager()->flush();

        return $deactivationUser->getId();
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