<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\FollowUser;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;
use Exception;

class UserRepository extends EntityRepository
{

    private function isGreatUser(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if(OrmConnector::getInstance()->getRepository(DeactivationUser::class)->checkDeactivation($user) || OrmConnector::getInstance()->getRepository(DeactivationUser::class)->checkFinalDeactivation($user)) {
            return false;
        }

        return $user->isActive();
    }


    public function getLoggedUser(): User {
        $UUID = App::getUserUuid();

        if ($UUID === null) {
            throw new EndpointException("User not logged", 403);
        }

        return $this->findByUuid($UUID);
    }

    public function findByUuid(string $uuid): User
    {
        $user = $this->find($uuid);
        if ($user === null) {
            throw new EndpointException("Compte introuvable", 404);
        }

        if(!$this->isGreatUser($user)) {
            throw new EndpointException("Compte inaccessible", 404);
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUser(?string $username, ?string $password, ?string $firstname, ?string $lastname, ?string $mail, ?string $formation_id, ?string $dateofbirth, ?string $profilePath, ?string $bannerPath): User
    {

        $token = bin2hex(random_bytes(32));

        if ($this->findOneBy(['username' => $username]) !== null) {
            throw new EndpointException("Le nom d'utilisateur existe déjà", 403);
        }

        if ($this->findOneBy(['mail' => $mail]) !== null) {
            throw new EndpointException("L'adresse mail existe déjà", 403);
        }

        try {
            $date = new DateTime($dateofbirth);
        } catch (Exception $e) {
            throw new EndpointException("Date de naissance invalide", 403);
        }

        try {
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->find($formation_id);
        } catch (Exception $e) {
            throw new EndpointException("Formation not found", 404);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setMail($mail);
        $user->setFormation($formation);
        $user->setDate($date);
        $user->setBiography(null);
        $user->setIsActive(true);
        $user->setToken($token);

        if($profilePath !== "" && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($profilePath)) {
            $user->setProfilePicturePath($profilePath);
        }

        if($bannerPath !== "" && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($bannerPath)) {
            $user->setBannerPath($bannerPath);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        OrmConnector::getInstance()->getRepository(Login::class)->createLogin($user, $password);

        return $user;
    }

    public function findByUsername(string $username): array
    {
        OrmConnector::getInstance()->getRepository(SearchHistoryUser::class)->addToSearchHistory($username);

        return $this->createQueryBuilder('u')
            ->select('u')
            ->where("u.isActive = 1")
            ->andWhere("u.username LIKE '%$username%'")
            ->getQuery()
            ->getResult();
    }

    public function editUser(
        ?string $username,
        ?string $profilePath,
        ?string $bannerPath,
        ?string $firstname,
        ?string $lastname,
        ?string $biography,
        ?int $formationId,
        ?int $sendTimestamp
    ): User
    {

        if($sendTimestamp === 0) {
            throw new EndpointException("Edit User need a valid timestamp", 403);
        }

        $user = self::getLoggedUser();

        if($username !== null && $username !== "" && $username !== $user->getUsername()) {
            if($this->findOneBy(['username' => $username]) !== null) {
                throw new EndpointException("Username already exists", 403);
            }
            $user->setUsername($username);
        }

        if($profilePath !== null && $profilePath !== "" && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($profilePath)) {
            $user->setProfilePicturePath($profilePath);
        }

        if($bannerPath !== null && $bannerPath !== "" && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($bannerPath)) {
            $user->setBannerPath($bannerPath);
        }

        if($firstname !== null && $firstname !== "" && $firstname !== $user->getFirstname()) {
            $user->setFirstname($firstname);
        }

        if($lastname !== null && $lastname !== "" && $lastname !== $user->getLastname()) {
            $user->setLastname($lastname);
        }

        if($biography !== null && $biography !== "" && $biography !== $user->getBiography()) {
            $user->setBiography($biography);
        }

        if($formationId !== null && $formationId !== 0 && $formationId !== $user->getFormation()->getId()) {
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
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'bannerPath' => $user->getBannerPath(),
            'followers' => OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowerCount($user),
            'following' => OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowingCount($user),
            'formation' => OrmConnector::getInstance()->getRepository(Formation::class)->toJson($user->getFormation()),
            'isActive' => $user->isActive(),
        ];
    }

    public function toJsonNames(User $user): array
    {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName()
        ];
    }

    public function toJson(User $user): array
    {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'bannerPath' => $user->getBannerPath(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'followers' => OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowerCount($user),
            'following' => OrmConnector::getInstance()->getRepository(FollowUser::class)->getFollowingCount($user),
            'formation' => OrmConnector::getInstance()->getRepository(Formation::class)->toJson($user->getFormation()),
            'mail' => $user->getMail(),
            'date' => $user->getDate()?->format('d-m-Y'),
            'biography' => $user->getBiography(),
            'isActive' => $user->isActive(),
        ];
    }
}