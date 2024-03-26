<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\User\User;
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

    public function create(?string $name, ?string $userUUID): Group|int {

        if(empty($name) || empty($userId)) {
            return 404;
        }

        $admin = App::getOrmManager()->connect()->getRepository(User::class)->find($userUUID);

        if ($admin === null) {
            return 404;
        }

        $group = new Group();
        $group->setName($name);
        $group->setAdmin($admin);
        $group->setCreationDate(new DateTime());
        $group->setIsActive(true);

        App::getOrmManager()->connect()->persist($group);
        App::getOrmManager()->connect()->flush();

        return $group;
    }

    public function toJson(Group $group): array {
        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'admin' => App::getOrmManager()->connect()->getRepository(User::class)->toJson($group->getAdmin()),
            'creationDate' => $group->getCreationDate()->format('d-m-Y H:i:s'),
            'isActive' => $group->isActive()
        ];
    }

}