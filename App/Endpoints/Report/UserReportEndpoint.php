<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\UserReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class UserReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/user', name: 'getAllUserReports', auth: true)]
    #[OA\Get(
        path: "/report/user",
        summary: "getAllUserReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All user reports retrieved")])]
    private function getAllUserReports(): void
    {
        $response = JsonBuilder::build();

        try {
            $userReports = OrmConnector::getInstance()->getRepository(UserReport::class)->findAll();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

        $data = [];
        foreach ($userReports as $report) {
            $data[] = OrmConnector::getInstance()->getRepository(UserReport::class)->toJson($report);
        }

        $response = JsonBuilder::build()->setCode(200);
        $response->addData('user_reports', $data);
        $response->getResult();
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
        $response = JsonBuilder::build();

        try {
            $reportedUUID = $_POST['reported_uuid'];
            $reportCategoryId = $_POST['report_category_id'];
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'];

            $userReport = OrmConnector::getInstance()->getRepository(UserReport::class)->create($reportedUUID, $reportCategoryId, $comment, $date);
            $userReportData = OrmConnector::getInstance()->getRepository(UserReport::class)->toJson($userReport);

            foreach ($userReportData as $key => $value) {
                $response->addData($key, $value);
            }

            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Delete('/report/user/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deleteUserReport', auth: true)]
    #[OA\Delete(
        path: "/report/user/{reportId}/delete",
        summary: "deleteUserReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deleteUserReport(int $reportId): void
    {
        $response = JsonBuilder::build();

        try {
            $userReport = OrmConnector::getInstance()->getRepository(UserReport::class)->delete($reportId);

            $response->addData("id", $userReport);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}