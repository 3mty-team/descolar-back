<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\App;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\Report\GroupMessageReport;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\User\UserRepository;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class GroupMessageReportRepository extends EntityRepository
{
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
    public function create(int $groupMessageId, int $reportCategoryId, ?string $comment, int $date): GroupMessageReport
    {

        if (empty($groupMessageId) || empty($reportCategoryId)) {
            throw new EndpointException('Missing parameters "groupMessageId" or "reportCategory"', 400);
        }

        $groupMessage = OrmConnector::getInstance()->getRepository(GroupMessage::class)->find($groupMessageId);
        if ($groupMessage === null){
            throw new EndpointException('Message not found', 400);
        }

        $reporter = UserRepository::getLoggedUser();
        if ($reporter === null) {
            throw new EndpointException('User not logged', 403);
        }

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $groupMessageReport = new GroupMessageReport();
        $groupMessageReport->setGroupMessage($groupMessage);
        $groupMessageReport->setReporter($reporter);
        $groupMessageReport->setReportCategory($reportCategory);
        $groupMessageReport->setComment($comment);
        $groupMessageReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $groupMessageReport->setIsActive(true);

        OrmConnector::getInstance()->persist($groupMessageReport);
        OrmConnector::getInstance()->flush();

        return $groupMessageReport;
    }

    public function delete(int $groupMessageReportId): int
    {
        $groupMessageReport = $this->find($groupMessageReportId);

        if ($groupMessageReport === null) {
            throw new EndpointException('Group message report not found', 404);
        }

        $groupMessageReport->setIsActive(false);

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