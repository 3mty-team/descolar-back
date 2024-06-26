<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\Report\GroupMessageReport;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class GroupMessageReportRepository extends EntityRepository
{
    public function findById(?int $id): GroupMessageReport
    {
        $groupMessageReport = $this->find($id);

        if ($groupMessageReport === null || !$groupMessageReport->isActive()) {
            throw new EndpointException('Group message report not found', 404);
        }

        return $groupMessageReport;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('gmr')
            ->select('gmr')
            ->where('gmr.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function create(?int $groupMessageId, ?int $reportCategoryId, ?string $comment, ?int $date): GroupMessageReport
    {

        $groupMessage = OrmConnector::getInstance()->getRepository(GroupMessage::class)->findById($groupMessageId);

        $reporter = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $groupMessageReport = new GroupMessageReport();
        $groupMessageReport->setGroupMessage($groupMessage);
        $groupMessageReport->setReporter($reporter);
        $groupMessageReport->setReportCategory($reportCategory);
        $groupMessageReport->setComment($comment);
        $groupMessageReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $groupMessageReport->setIsActive(true);

        Validator::getInstance($groupMessageReport)->check();

        OrmConnector::getInstance()->persist($groupMessageReport);
        OrmConnector::getInstance()->flush();

        return $groupMessageReport;
    }

    public function delete(int $groupMessageReportId): int
    {
        $groupMessageReport = $this->findById($groupMessageReportId);

        $groupMessageReport->setIsActive(false);

        Validator::getInstance($groupMessageReport)->check();

        OrmConnector::getInstance()->flush();

        return $groupMessageReportId;
    }

    public function toJson(GroupMessageReport $groupMessageReport): array
    {
        return [
            'id' => $groupMessageReport->getId(),
            'groupMessageId' => $groupMessageReport->getGroupMessage()->getId(),
            'messageContent' => $groupMessageReport->getGroupMessage()->getContent(),
            'userReported' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupMessageReport->getGroupMessage()->getUser()),
            'userReporter' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupMessageReport->getReporter()),
            'reportCategory' => $groupMessageReport->getReportCategory()->getName(),
            'comment' => $groupMessageReport->getComment(),
            'date' => $groupMessageReport->getDate(),
            'isActive' => $groupMessageReport->isActive()
        ];
    }
}