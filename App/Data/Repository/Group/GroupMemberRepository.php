<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\Group\GroupMember;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class GroupMemberRepository extends EntityRepository
{
    private function checkIfUserAlreadyRegistered(int $groupId, string $userUUID): ?GroupMember
    {
        return $this->findOneBy(['group' => $groupId, 'user' => $userUUID]);
    }

    private function checkGroupMemberExists(int $groupId, string $userUUID): ?GroupMember
    {
        return $this->findOneBy(['group' => $groupId, 'user' => $userUUID, 'isActive' => 1]);
    }

    public function getUsersByGroup(Group $group): array
    {
        return $this->findBy(['group' => $group, 'isActive' => 1]);
    }

    public function addMemberInGroup(int $groupId, string $userUUID, ?string $date): GroupMember
    {
        if(empty($userUUID) || empty($date)) {
            throw new EndpointException('Missing parameters "userId" or "date"', 400);
        }

        $user = App::getOrmManager()->connect()->getRepository(User::class)->find($userUUID);
        if($user === null) {
            throw new EndpointException('User not found', 404);
        }

        $group = App::getOrmManager()->connect()->getRepository(Group::class)->find($groupId);
        if($group === null) {
            throw new EndpointException('Group not found', 404);
        }

        if($this->checkGroupMemberExists($groupId, $userUUID) !== null) {
            throw new EndpointException('User already in group', 400);
        }

        if(($gm = $this->checkIfUserAlreadyRegistered($groupId, $userUUID)) !== null) {

            $gm->setIsActive(true);
            $gm->setJoinDate(new DateTime($date));
            App::getOrmManager()->connect()->persist($gm);
            App::getOrmManager()->connect()->flush();
            return $gm;

        }

        $groupMember = new GroupMember();
        $groupMember->setGroup($group);
        $groupMember->setUser($user);
        $groupMember->setJoinDate(new DateTime($date));
        $groupMember->setIsActive(true);

        App::getOrmManager()->connect()->persist($groupMember);
        App::getOrmManager()->connect()->flush();

        return $groupMember;
    }

    public function removeMemberInGroup(int $groupId, ?string $userUUID): GroupMember
    {
        if(empty($userUUID)) {
            throw new EndpointException('Missing parameters "userId"', 400);
        }

        $groupMember = $this->checkGroupMemberExists($groupId, $userUUID);
        if($groupMember === null) {
            throw new EndpointException('User not in group', 400);
        }

        $groupMember->setIsActive(false);

        App::getOrmManager()->connect()->persist($groupMember);
        App::getOrmManager()->connect()->flush();

        return $groupMember;
    }

    public function toJson(?int $groupId): array
    {
        $group = App::getOrmManager()->connect()->getRepository(Group::class)->findById($groupId);

        $groupMembers = $this->getUsersByGroup($group);
        $userData = [];
        foreach ($groupMembers as $groupMember) {
            /** @var GroupMember $groupMember */
            $userData[] = App::getOrmManager()->connect()->getRepository(User::class)->toReduceJson($groupMember->getUser());
        }

        return [
            'group' => App::getOrmManager()->connect()->getRepository(Group::class)->toJson($group),
            'users' => $userData
        ];
    }

}