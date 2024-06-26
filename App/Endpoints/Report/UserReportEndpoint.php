<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\UserReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class UserReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/user', name: 'getAllUserReports', moderationAuth: true)]
    #[OA\Get(
        path: "/report/user",
        summary: "getAllUserReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All user reports retrieved")])]
    private function getAllUserReports(): void
    {
        $this->reply(function ($response) {
            $userReports = OrmConnector::getInstance()->getRepository(UserReport::class)->findAll();

            $data = [];
            foreach ($userReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(UserReport::class)->toJson($report);
            }

            $response->addData('user_reports', $data);
        });
    }

    #[Post('/report/user/create', name: 'createUserReport', auth: true)]
    #[OA\Post(
        path: "/report/user/create",
        summary: "createUserReport",
        tags: ["Report"],
        responses: [
            new OA\Response(response: 200, description: "User report added"),
            new OA\Response(response: 404, description: "User not found")]
    )]
    private function createUserReport(): void
    {
        $this->reply(function ($response) {
            [$reportedUUID, $reportCategoryId, $comment, $date] = Requester::getInstance()->trackMany(
                "reported_uuid", "report_category_id", "comment", "date"
            );

            $userReport = OrmConnector::getInstance()->getRepository(UserReport::class)->create($reportedUUID, $reportCategoryId, $comment, $date);
            $userReportData = OrmConnector::getInstance()->getRepository(UserReport::class)->toJson($userReport);

            foreach ($userReportData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/report/user/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deleteUserReport', moderationAuth: true)]
    #[OA\Delete(
        path: "/report/user/{reportId}/delete",
        summary: "deleteUserReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deleteUserReport(int $reportId): void
    {
        $this->reply(function ($response) use ($reportId) {
            $userReport = OrmConnector::getInstance()->getRepository(UserReport::class)->delete($reportId);

            $response->addData("id", $userReport);
        });
    }
}