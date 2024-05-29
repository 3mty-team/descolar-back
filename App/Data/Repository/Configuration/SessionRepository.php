<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Session;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function createSession($date, $localisation, $userAgent): ?Session
    {

        if (empty($date) || empty($localisation) || empty($userAgent)) {
            throw new EndpointException("Missing parameters", 400);
        }

        /*
         * @var User $user
         */
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

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

    public function toJson(Session $session): array
    {
        return [
            'id' => $session->getId(),
            'date' => $session->getDate(),
            'user_id' => $session->getUser()->getUuid(),
            'localisation' => $session->getLocalisation(),
            'user_agent' => $session->getUserAgent(),
            'is_active' => $session->isActive()
        ];
    }

    public function getSessionByUuid(string $sessionUuid): Session
    {
        $session = $this->findOneBy(['id' => $sessionUuid]);
        if ($session === null) {
            throw new EndpointException("Session not found", 404);
        }

        return $session;
    }
}