<?php

namespace Descolar\Data\Repository\Report;

use DateTime;
use DateTimeZone;
use Descolar\Data\Entities\Post\Post;
use Descolar\Data\Entities\Report\PostReport;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class PostReportRepository extends EntityRepository
{

    public function findById(?int $id): PostReport
    {
        $postReport = $this->find($id);

        if ($postReport === null || !$postReport->isActive()) {
            throw new EndpointException('Post report not found', 404);
        }

        return $postReport;
    }

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
    public function create(?int $postId, ?int $reportCategoryId, ?string $comment, ?int $date): PostReport
    {

        $post = OrmConnector::getInstance()->getRepository(Post::class)->findById($postId);

        $reporter = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $reportCategory = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findById($reportCategoryId);

        $postReport = new PostReport();
        $postReport->setPost($post);
        $postReport->setReporter($reporter);
        $postReport->setReportCategory($reportCategory);
        $postReport->setComment($comment);
        $postReport->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $postReport->setIsActive(true);

        Validator::getInstance($postReport)->check();

        OrmConnector::getInstance()->persist($postReport);
        OrmConnector::getInstance()->flush();

        return $postReport;
    }

    public function delete(int $postReportId): int
    {
        $postReport = $this->findById($postReportId);

        $postReport->setIsActive(false);

        Validator::getInstance($postReport)->check();

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