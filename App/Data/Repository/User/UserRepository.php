<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;
use Exception;

class UserRepository extends EntityRepository
{

    /**
     * @throws Exception
     */
    public function createUser(String $username, String $password, String $firstname, String $lastname, String $mail, String $formation_id, String $dateofbirth, string $token): User
    {
        try {
            $date = new DateTime($dateofbirth);
        } catch (Exception $e) {
            throw new EndpointException("Invalid date of birth", 400);
        }

        try {
            $formation = App::getOrmManager()->connect()->getRepository(Formation::class)->find($formation_id);
        } catch (Exception $e) {
            throw new EndpointException("Formation not found", 400);
        }

        $user = new User();
        try {
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
        } catch (Exception $e) {
            throw new EndpointException("User creation failed: " . $e->getMessage(), 400);
        }

        try {
            App::getOrmManager()->connect()->getRepository(Login::class)->createLogin($user, $password);
        } catch (Exception $e) {
            throw new EndpointException("Login creation failed: " . $e->getMessage(), 400);
        }

        return $user;
    }

    public function verifyToken(String $token): ?User {
        $user = $this->findOneBy(['token' => $token]);
        if($user === null) {
            throw new EndpointException("Token not found", 404);
        }

        $user->setToken(null);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function toReduceJson(User $user): array {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'isActive' => $user->isActive(),
        ];
    }

    public function toJson(User $user): array {
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