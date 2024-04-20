<?php

namespace Descolar\Data\Repository\Configuration;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function createSessionn($date, $localisation, $userAgent): ?Session
    {
        /*
         * @var User $user
         */
        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(["uuid" => App::getUserUuid()]);

        if ($user === null) {
            return null;
        }

        $date = new DateTime();

        $session = new Session();
        $session->setUser($user);
        $session->setDate($date);
        $session->setLocalisation($localisation);
        $session->setUserAgent($userAgent);
        $session->setIsActive(true);

        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();

        return $this->findOneBy(['date' => $date, 'localisation' => $localisation, 'userAgent' => $userAgent]);
    }

    public function toJson(Session $session): array {
        return [
            'id' => $session->getId(),
            'date' => $session->getDate(),
            'user_id' => $session->getUser()->getUuid(),
            'localisation' => $session->getLocalisation(),
            'user_agent' => $session->getUserAgent(),
            'is_active' => $session->isActive()
        ];
    }

    public function getSessionByUuid(String $sessionUuid): ?Session
    {
        return $this->findOneBy(['id' => $sessionUuid]);
    }
}