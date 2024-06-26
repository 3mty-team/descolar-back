<?php

namespace Descolar\Data\Repository\Institution;

use Descolar\Data\Entities\Institution\Diploma;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class FormationRepository extends EntityRepository
{
    public function findAll(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->where('f.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    public function findById(?int $id): Formation
    {
        $formation = $this->find($id);

        if ($formation === null) {
            throw new EndpointException("Formation not found", 404);
        }

        return $formation;
    }

    public function toJson(Formation $formation): array
    {
        return [
            'id' => $formation->getId(),
            'diploma' => OrmConnector::getInstance()->getRepository(Diploma::class)->toJson($formation->getDiploma()),
            'name' => $formation->getName(),
            'shortName' => $formation->getShortName(),
            'isActive' => $formation->isActive()
        ];
    }
}