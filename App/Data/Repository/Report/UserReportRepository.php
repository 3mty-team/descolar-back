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
        try{
            $this->createQueryBuilder('ur')
                ->select('ur')
                ->where('ur.isActive = 1')
                ->getQuery()
                ->getResult();
        }catch (\Exception $e){
            throw new EndpointException($e);
        }

        return $this->createQueryBuilder('ur')
            ->select('ur')
            ->where('ur.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function create(string $reportedUUID, int $reportCategoryId, ?string $comment, int $date): UserReport
    {

        if (empty($reportedUUID) || empty($reportCategoryId)) {
            throw new EndpointException('Missing parameters "reported" or "reportCategory"', 400);
        }

        $reporterUUID = App::getUserUuid();
        if ($reporterUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $reported = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($reportedUUID);
        $reporter = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($reporterUUID);

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

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

        $userReport->setIsActive(false);

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