<?php

namespace Descolar\Data\Repository\Configuration;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function createSession(?string $date, ?string $localisation, ?string $userAgent): ?Session
    {
        /*
         * @var User $user
         */
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $session = new Session();
        $session->setUser($user);
        $session->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $session->setLocalisation($localisation);
        $session->setUserAgent($userAgent);
        $session->setIsActive(true);

        Validator::getInstance($session)->check();

        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();

        return $session;
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

    public function getSessionByUuid(?string $sessionUuid): Session
    {
        $session = $this->findOneBy(['id' => $sessionUuid]);
        if ($session === null) {
            throw new EndpointException("Session not found", 404);
        }

        return $session;
    }
}