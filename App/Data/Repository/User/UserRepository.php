<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\Group\GroupMember;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\DeactivationUser;
use Descolar\Data\Entities\User\FollowUser;
use Descolar\Data\Entities\User\SearchHistoryUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;
use Exception;

class UserRepository extends EntityRepository
{

    private function isGreatUser(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if (OrmConnector::getInstance()->getRepository(DeactivationUser::class)->checkDeactivation($user) || OrmConnector::getInstance()->getRepository(DeactivationUser::class)->checkFinalDeactivation($user)) {
            return false;
        }

        return $user->isActive();
    }


    public function getLoggedUser(): User
    {
        $userUUID = App::getUserUuid();

        if ($userUUID === null) {
            throw new EndpointException("User not logged", 403);
        }

        return $this->findByUuid($userUUID);
    }

    public function findByUuid(?string $userUUID): User
    {
        $user = $this->find($userUUID);
        if ($user === null) {
            throw new EndpointException("Compte introuvable", 404);
        }

        if (!$this->isGreatUser($user)) {
            throw new EndpointException("Compte inaccessible", 404);
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUser(?string $username, ?string $password, ?string $firstname, ?string $lastname, ?string $mail, ?string $formationId, ?string $dateofbirth, ?string $profilePath, ?string $bannerPath): User
    {

        $token = bin2hex(random_bytes(32));

        $date = new DateTime($dateofbirth, new \DateTimeZone('Europe/Paris'));
        $formation = OrmConnector::getInstance()->getRepository(Formation::class)->findById($formationId);

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

        if ($profilePath !== null && OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($profilePath)) {
            $user->setProfilePicturePath($profilePath);
        }

        if ($bannerPath !== null && OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($bannerPath)) {
            $user->setBannerPath($bannerPath);
        }

        Validator::getInstance($user)->check();

        OrmConnector::getInstance()->persist($user);
        OrmConnector::getInstance()->flush();

        OrmConnector::getInstance()->getRepository(Login::class)->createLogin($user, $password);

        $date = new DateTime('now', new \DateTimeZone('Europe/Paris'));
        $stringDate = $date->getTimestamp();
        OrmConnector::getInstance()->getRepository(GroupMember::class)->addMemberToGroup($formationId, $user->getUUID(), $stringDate);

        return $user;
    }

    public function findByUsername(?string $username): array
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
        ?int    $formationId,
        ?int    $sendTimestamp
    ): User
    {
        $user = self::getLoggedUser();

        if($username !== null) {
            $user->setUsername($username);
        }

        if ($profilePath !== null && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($profilePath)) {
            $user->setProfilePicturePath($profilePath);
        }

        if ($bannerPath !== null && $media = OrmConnector::getInstance()->getRepository(Media::class)->findByUrl($bannerPath)) {
            $user->setBannerPath($bannerPath);
        }

        if ($firstname !== null && $firstname !== $user->getFirstname()) {
            $user->setFirstname($firstname);
        }

        if ($lastname !== null && $lastname !== $user->getLastname()) {
            $user->setLastname($lastname);
        }

        if ($biography !== null && $biography !== $user->getBiography()) {
            $user->setBiography($biography);
        }

        if ($formationId !== null && $formationId !== $user->getFormation()->getId()) {
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->findById($formationId);

            // For formation groups, formation_id = group_id
            OrmConnector::getInstance()->getRepository(GroupMember::class)->removeMemberOfGroup($user->getFormation()->getId(), $user->getUUID());
            OrmConnector::getInstance()->getRepository(GroupMember::class)->addMemberToGroup($formationId, $user->getUUID(), $sendTimestamp);

            $user->setFormation($formation);
        }

        Validator::getInstance($user)->check();

        OrmConnector::getInstance()->persist($user);
        OrmConnector::getInstance()->flush();

        return $user;
    }

    public function deleteUser(): string
    {
        $user = self::getLoggedUser();

        $deactivationUser = OrmConnector::getInstance()->getRepository(DeactivationUser::class)->findOneBy(['user' => $user]);
        if ($deactivationUser !== null) {
            $deactivationUser->setIsActive(false);
            $deactivationUser->setIsFinal(true);

            Validator::getInstance($deactivationUser)->check();

            OrmConnector::getInstance()->persist($deactivationUser);
            OrmConnector::getInstance()->flush();
        }

        $user->setIsActive(false);

        Validator::getInstance($user)->check();
        OrmConnector::getInstance()->persist($user);
        OrmConnector::getInstance()->flush();

        return $user->getUUID();
    }

    public function verifyToken(string $token): ?User
    {
        $user = $this->findOneBy(['token' => $token]);
        if ($user === null) {
            throw new EndpointException("Token not found", 404);
        }

        $user->setToken(null);

        Validator::getInstance($user)->check();

        OrmConnector::getInstance()->persist($user);
        OrmConnector::getInstance()->flush();

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