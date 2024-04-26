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
use Doctrine\ORM\EntityRepository;

class UserReportRepository extends EntityRepository
{
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
    public function create(string $reportedUUID, int $reportCategoryId, string $comment, int $date): UserReport
    {

        if (empty($reportedUUID) || empty($reportCategoryId)) {
            throw new EndpointException('Missing parameters "reported" or "reportCategory"', 400);
        }

        $reporterUUID = App::getUserUuid();
        if ($reporterUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $reported = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $reportedUUID]);
        $reporter = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $reporterUUID]);
        if ($reported === null || $reporter === null) {
            throw new EndpointException('Reported user or reporter user not found', 404);
        }

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findOneBy(['id' => $reportCategoryId]);
        if ($reportCategory === null) {
            throw new EndpointException('Report category not found', 404);
        }

        $userReport = new UserReport();
        $userReport->setReported($reported);
        $userReport->setReporter($reporter);
        $userReport->setReportCategory($reportCategory);
        $userReport->setComment($comment);
        $userReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $userReport->setIsActive(true);

        OrmConnector::getInstance()->persist($userReport);
        OrmConnector::getInstance()->flush();

        return $userReport;
    }


    public function delete(int $userReportId)
    {
        $userReport = $this->find($userReportId);

        if ($userReport === null) {
            throw new EndpointException('User report not found', 404);
        }

        $userReport->setIsActive(0);

        OrmConnector::getInstance()->flush();

        return $userReportId;
    }

    public function toJson(UserReport $userReport): array
    {
        return [
            'id' => $userReport->getId(),
            'userReported' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($userReport->getReported()),
            'reportCategory' => $userReport->getReportCategory()->getName(),
            'comment' => $userReport->getComment(),
            'date' => $userReport->getDate(),
            'isActive' => $userReport->isActive()
        ];
    }
}