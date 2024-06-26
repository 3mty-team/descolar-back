<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
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

    public function findById(?int $id): Group
    {
        $group = $this->find($id);

        if ($group === null) {
            throw new EndpointException("Group not found", 404);
        }

        return $group;
    }

    public function create(?string $name, ?string $userUUID): Group {

        $admin = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        $group = new Group();
        $group->setName($name);
        $group->setAdmin($admin);
        $group->setCreationDate(new DateTime());
        $group->setIsActive(true);

        Validator::getInstance($group)->check();

        OrmConnector::getInstance()->persist($group);
        OrmConnector::getInstance()->flush();

        return $group;
    }

    public function editGroup(?int $id, ?string $name, ?string $userUUID): Group {

        $group = $this->findById($id);
        $admin = OrmConnector::getInstance()->getRepository(User::class)->findByUUID($userUUID);

        if(!empty($name)) {
            $group->setName($name);
        }

        if(!empty($userUUID)) {
            $group->setAdmin($admin);
        }

        Validator::getInstance($group)->check();

        OrmConnector::getInstance()->flush();

        return $group;
    }

    public function deleteGroup(?int $id): int {

        $group = $this->findById($id);

        $group->setIsActive(false);

        Validator::getInstance($group)->check();

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