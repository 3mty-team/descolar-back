<?php

namespace Descolar\Data\Repository\Report;

use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class ReportCategoryRepository extends EntityRepository
{
    public function findById(int $id): ReportCategory
    {
        $reportCategory = $this->find($id);

        if ($reportCategory === null) {
            throw new EndpointException('Report category not found', 404);
        }

        return $reportCategory;
    }
}