<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\App;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Report\PostReport;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class PostReportRepository extends EntityRepository
{
    public function findAll(): array
    {
        return $this->createQueryBuilder('pr')
            ->select('pr')
            ->where('pr.isActive = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function create(int $postId, int $reportCategoryId, ?string $comment, int $date): PostReport
    {

        if (empty($postId) || empty($reportCategoryId)) {
            throw new EndpointException('Missing parameters "postId" or "reportCategory"', 400);
        }

        $post = OrmConnector::getInstance()->getRepository(Post::class)->find($postId);

        $reporterUUID = App::getUserUuid();
        if ($reporterUUID === null) {
            throw new EndpointException('User not logged', 403);
        }

        $reporter = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($reporterUUID);
        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $postReport = new PostReport();
        $postReport->setPost($post);
        $postReport->setReporter($reporter);
        $postReport->setReportCategory($reportCategory);
        $postReport->setComment($comment);
        $postReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $postReport->setIsActive(true);

        OrmConnector::getInstance()->persist($postReport);
        OrmConnector::getInstance()->flush();

        return $postReport;
    }

    public function delete(int $postReportId): int
    {
        $postReport = $this->find($postReportId);

        if ($postReport === null) {
            throw new EndpointException('User report not found', 404);
        }

        $postReport->setIsActive(false);

        OrmConnector::getInstance()->flush();

        return $postReportId;
    }

    public function toJson(PostReport $postReport): array
    {
        return [
            'id' => $postReport->getId(),
            'postId' => $postReport->getPost()->getId(),
            'postContent' => $postReport->getPost()->getContent(),
            'userReported' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($postReport->getPost()->getUser()),
            'userReporter' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($postReport->getReporter()),
            'reportCategory' => $postReport->getReportCategory()->getName(),
            'comment' => $postReport->getComment(),
            'date' => $postReport->getDate(),
            'isActive' => $postReport->isActive()
        ];
    }
}