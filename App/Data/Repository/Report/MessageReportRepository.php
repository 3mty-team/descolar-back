<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Report\MessageReport;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\User\MessageUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class MessageReportRepository extends EntityRepository
{

    public function findById(?int $id) : MessageReport
    {
        $messageReport = $this->find($id);

        if ($messageReport === null || !$messageReport->isActive()) {
            throw new EndpointException('Message report not found', 404);
        }

        return $messageReport;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('mr')
            ->select('mr')
            ->where('mr.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function create(?int $messageId, ?int $reportCategoryId, ?string $comment, ?int $date): MessageReport
    {

        $message = OrmConnector::getInstance()->getRepository(MessageUser::class)->findById($messageId);

        $reporter = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $messageReport = new messageReport();
        $messageReport->setMessage($message);
        $messageReport->setReporter($reporter);
        $messageReport->setReportCategory($reportCategory);
        $messageReport->setComment($comment);
        $messageReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $messageReport->setIsActive(true);

        Validator::getInstance($messageReport)->check();

        OrmConnector::getInstance()->persist($messageReport);
        OrmConnector::getInstance()->flush();

        return $messageReport;
    }

    public function delete(int $messageReportId): int
    {
        $messageReport = $this->findById($messageReportId);

        $messageReport->setIsActive(false);

        Validator::getInstance($messageReport)->check();

        OrmConnector::getInstance()->flush();

        return $messageReportId;
    }

    public function toJson(MessageReport $messageReport): array
    {
        return [
            'id' => $messageReport->getId(),
            'messageId' => $messageReport->getMessage()->getId(),
            'messageContent' => $messageReport->getMessage()->getContent(),
            'userReported' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($messageReport->getMessage()->getSender()),
            'userReporter' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($messageReport->getReporter()),
            'reportCategory' => $messageReport->getReportCategory()->getName(),
            'comment' => $messageReport->getComment(),
            'date' => $messageReport->getDate(),
            'isActive' => $messageReport->isActive()
        ];
    }
}