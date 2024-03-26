<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Session;
use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function createSession($date, $localisation, $userAgent): void
    {
        $session = new Session();
        $session->setDate($date);
        $session->setLocalisation($localisation);
        $session->setUserAgent($userAgent);

        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }
}