<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{

    public function findAll(): array
    {
        return $this->createQueryBuilder('g')
            ->select('g')
            ->where('g.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): Group|int
    {
        $group = $this->find($id);

        if ($group === null) {
            throw new EndpointException("Group not found", 404);
        }

        return $group;
    }

    public function create(?string $name, ?string $userUUID): Group {

        if(empty($name) || empty($userUUID)) {
            throw new EndpointException('Missing parameters "name" or "admin"', 400);
        }

        $admin = OrmConnector::getInstance()->getRepository(User::class)->find($userUUID);

        if ($admin === null) {
            throw new EndpointException('User not found', 404);
        }

        $group = new Group();
        $group->setName($name);
        $group->setAdmin($admin);
        $group->setCreationDate(new DateTime());
        $group->setIsActive(true);

        OrmConnector::getInstance()->persist($group);
        OrmConnector::getInstance()->flush();

        return $group;
    }

    public function editGroup(?int $id, ?string $name, ?string $userUUID): Group {

        if(empty($id) || (empty($name) && empty($userUUID))) {
            throw new EndpointException('Missing parameter "id" or ["name" or "admin"]', 400);
        }

        $group = $this->find($id);
        $admin = OrmConnector::getInstance()->getRepository(User::class)->find($userUUID);

        if($group === null || $admin === null) {
            throw new EndpointException('Group or User not found', 404);
        }

        if(!empty($name)) {
            $group->setName($name);
        }

        if(!empty($userUUID)) {
            $group->setAdmin($admin);
        }

        OrmConnector::getInstance()->flush();

        return $group;
    }

    public function deleteGroup(?int $id): int {

        if(empty($id)) {
            throw new EndpointException('Missing parameter "id"', 400);
        }

        $group = $this->find($id);

        if($group === null) {
            throw new EndpointException('Group not found', 404);
        }

        $group->setIsActive(false);

        OrmConnector::getInstance()->flush();

        return $id;
    }

    public function toJson(Group $group): array {
        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'admin' => OrmConnector::getInstance()->getRepository(User::class)->toJson($group->getAdmin()),
            'creationDate' => $group->getCreationDate()->format('d-m-Y H:i:s'),
            'isActive' => $group->isActive()
        ];
    }

}