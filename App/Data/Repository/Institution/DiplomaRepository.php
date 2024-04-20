<?php

namespace Descolar\Data\Repository\Institution;

use Descolar\Data\Entities\Institution\Diploma;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class DiplomaRepository extends EntityRepository
{
    public function findAll(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): Diploma|int
    {
        $diploma = $this->find($id);

        if ($diploma === null) {
            throw new EndpointException("Diploma not found", 404);
        }

        return $diploma;
    }

    public function toJson(Diploma $diploma): array
    {
        return [
            'id' => $diploma->getId(),
            'name' => $diploma->getName(),
            'isActive' => $diploma->isActive()
        ];
    }
}