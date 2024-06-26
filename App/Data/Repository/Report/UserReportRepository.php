<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\App;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\Report\UserReport;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class UserReportRepository extends EntityRepository
{

    public function findById(?int $id): UserReport
    {
        $userReport = $this->find($id);

        if ($userReport === null || !$userReport->isActive()) {
            throw new EndpointException('User report not found', 404);
        }

        return $userReport;
    }

    public function findAll(): array
    {
	 return $this->createQueryBuilder('ur')
            ->select('ur')
            ->where('ur.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function create(?string $reportedUUID, ?int $reportCategoryId, ?string $comment, ?int $date): UserReport
    {
        $reporter = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        $reported = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($reportedUUID);

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $userReport = new UserReport();
        $userReport->setReported($reported);
        $userReport->setReporter($reporter);
        $userReport->setReportCategory($reportCategory);
        $userReport->setComment($comment);
        $userReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $userReport->setIsActive(true);

        Validator::getInstance($userReport)->check();

        OrmConnector::getInstance()->persist($userReport);
        OrmConnector::getInstance()->flush();

        return $userReport;
    }


    public function delete(int $userReportId)
    {
        $userReport = $this->findById($userReportId);

        $userReport->setIsActive(false);

        Validator::getInstance($userReport)->check();

        OrmConnector::getInstance()->flush();

        return $userReportId;
    }

    public function toJson(UserReport $userReport): array
    {
        return [
            'id' => $userReport->getId(),
            'userReported' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($userReport->getReported()),
            'userReporter' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($userReport->getReporter()),
            'reportCategory' => $userReport->getReportCategory()->getName(),
            'comment' => $userReport->getComment(),
            'date' => $userReport->getDate(),
            'isActive' => $userReport->isActive()
        ];
    }
}