<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;
use Exception;

class UserRepository extends EntityRepository
{

    public static function getLoggedUser(): ?User {
        $UUID = App::getUserUuid();

        if ($UUID === null) {
            return null;
        }

        return OrmConnector::getInstance()->getRepository(User::class)->find($UUID);
    }

    public function findByUuid(string $uuid): ?User
    {
        $user = $this->find($uuid);
        if ($user === null) {
            throw new EndpointException("User not found", 404);
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUser(string $username, string $password, string $firstname, string $lastname, string $mail, string $formation_id, string $dateofbirth, string $token): User
    {
        if ($this->findOneBy(['username' => $username]) !== null) {
            throw new EndpointException("Username already exists", 403);
        }

        if ($this->findOneBy(['mail' => $mail]) !== null) {
            throw new EndpointException("Mail already exists", 403);
        }

        try {
            $date = new DateTime($dateofbirth);
        } catch (Exception $e) {
            throw new EndpointException("Invalid date of birth", 403);
        }

        try {
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->find($formation_id);
        } catch (Exception $e) {
            throw new EndpointException("Formation not found", 404);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setProfilePicturePath(null);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setMail($mail);
        $user->setFormation($formation);
        $user->setDate($date);
        $user->setBiography(null);
        $user->setIsActive(true);
        $user->setToken($token);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        OrmConnector::getInstance()->getRepository(Login::class)->createLogin($user, $password);

        return $user;
    }

    public function editUser(
        string $username,
        string $profilePath,
        string $firstname,
        string $lastname,
        string $biography,
        int $formationId,
        int $sendTimestamp
    ): User
    {

        if($sendTimestamp === 0) {
            throw new EndpointException("Edit User need a valid timestamp", 403);
        }

        $user = self::getLoggedUser();
        if($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        if($username !== "" && $username !== $user->getUsername()) {
            if($this->findOneBy(['username' => $username]) !== null) {
                throw new EndpointException("Username already exists", 403);
            }
            $user->setUsername($username);
        }

        //TODO profile path

        if($firstname !== "" && $firstname !== $user->getFirstname()) {
            $user->setFirstname($firstname);
        }

        if($lastname !== "" && $lastname !== $user->getLastname()) {
            $user->setLastname($lastname);
        }

        if($biography !== "" && $biography !== $user->getBiography()) {
            $user->setBiography($biography);
        }

        if($formationId !== 0 && $formationId !== $user->getFormation()->getId()) {
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->find($formationId);
            if($formation === null) {
                throw new EndpointException("Formation not found", 404);
            }
            $user->setFormation($formation);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function deleteUser(): string
    {
        $user = self::getLoggedUser();
        if($user === null) {
            throw new EndpointException("User not logged", 403);
        }

        $deactivationUser = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->findOneBy(['user' => $user]);
        if($deactivationUser !== null) {
            $deactivationUser->setIsActive(false);
            $deactivationUser->setIsFinal(true);

            $this->getEntityManager()->persist($deactivationUser);
            $this->getEntityManager()->flush();
        }

        $user->setIsActive(false);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user->getUUID();
    }

    public function verifyToken(string $token): ?User
    {
        $user = $this->findOneBy(['token' => $token]);
        if ($user === null) {
            throw new EndpointException("Token not found", 404);
        }

        $user->setToken(null);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function toReduceJson(User $user): array
    {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'isActive' => $user->isActive(),
        ];
    }

    public function toJson(User $user): array
    {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'mail' => $user->getMail(),
            'date' => $user->getDate()?->format('d-m-Y'),
            'biography' => $user->getBiography(),
            'isActive' => $user->isActive(),
        ];
    }
}