<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\Group\GroupMember;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class GroupMemberRepository extends EntityRepository
{
    private function checkIfUserAlreadyRegistered(?int $groupId, ?string $userUUID): ?GroupMember
    {
        return $this->findOneBy(['group' => $groupId, 'user' => $userUUID]);
    }

    private function checkGroupMemberExists(?int $groupId, ?string $userUUID): ?GroupMember
    {
        return $this->findOneBy(['group' => $groupId, 'user' => $userUUID, 'isActive' => 1]);
    }

    public function getUsersByGroup(?Group $group): array
    {
        return $this->findBy(['group' => $group, 'isActive' => 1]);
    }

    /**
     * @throws \Exception
     */
    public function addMemberToGroup(?int $groupId, ?string $userUUID, ?string $date): GroupMember
    {

        $user = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);

        $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($groupId);

        if($this->checkGroupMemberExists($groupId, $userUUID) !== null) {
            throw new EndpointException('User already in group', 400);
        }

        if(($gm = $this->checkIfUserAlreadyRegistered($groupId, $userUUID)) !== null) {

            $gm->setIsActive(true);
            $gm->setJoinDate(new DateTime($date));

            Validator::getInstance($gm)->check();

            OrmConnector::getInstance()->persist($gm);
            OrmConnector::getInstance()->flush();
            return $gm;
        }

        $groupMember = new GroupMember();
        $groupMember->setGroup($group);
        $groupMember->setUser($user);
        $groupMember->setJoinDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $groupMember->setIsActive(true);

        Validator::getInstance($groupMember)->check();

        OrmConnector::getInstance()->persist($groupMember);
        OrmConnector::getInstance()->flush();

        return $groupMember;
    }

    public function removeMemberOfGroup(int $groupId, ?string $userUUID): GroupMember
    {

        $groupMember = $this->checkGroupMemberExists($groupId, $userUUID);
        if($groupMember === null) {
            throw new EndpointException('User not in group', 400);
        }

        $groupMember->setIsActive(false);

        Validator::getInstance($groupMember)->check();

        OrmConnector::getInstance()->persist($groupMember);
        OrmConnector::getInstance()->flush();

        return $groupMember;
    }

    public function toJson(?int $groupId): array
    {
        $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($groupId);

        $groupMembers = $this->getUsersByGroup($group);

        $userData = [];
        foreach ($groupMembers as $groupMember) {
            /** @var GroupMember $groupMember */
            $userData[] = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupMember->getUser());
        }

        return [
            'group' => OrmConnector::getInstance()->getRepository(Group::class)->toJson($group),
            'users' => $userData
        ];
    }

}