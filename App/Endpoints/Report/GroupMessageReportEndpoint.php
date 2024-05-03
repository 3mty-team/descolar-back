<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\GroupMessageReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class GroupMessageReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/groupmessage', name: 'getAllGroupMessageReports', auth: false)]
    #[OA\Get(
        path: "/report/groupmessage",
        summary: "getAllGroupMessageReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All group message reports retrieved")])]
    private function getAllGroupMessageReports(): void
    {
        $response = JsonBuilder::build();

        try {
            $groupMessageReports = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->findAll();

            $data = [];
            foreach ($groupMessageReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->toJson($report);
            }

            $response->setCode(200);
            $response->addData('group_message_reports', $data);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Post('/report/groupmessage/create', name: 'createGroupMessageReport', auth: false)]
    #[OA\Post(
        path: "/report/groupmessage/create",
        summary: "createGroupMessageReport",
        tags: ["Report"],
        responses: [
            new OA\Response(response: 200, description: "Group message report added"),
            new OA\Response(response: 404, description: "GroupMessage or User not found")]
    )]
    private function createGroupMessageReport(): void
    {
        $response = JsonBuilder::build();

        try {
            $groupMessageId = $_POST['group_message_id'] ?? 0;
            $reportCategoryId = $_POST['report_category_id'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'];

            $groupMessageReport = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->create($groupMessageId, $reportCategoryId, $comment, $date);
            $groupMessageReportData = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->toJson($groupMessageReport);

            foreach ($groupMessageReportData as $key => $value) {
                $response->addData($key, $value);
            }

            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('group_message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Delete('/report/groupmessage/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deleteGroupMessageReport', auth: false)]
    #[OA\Delete(
        path: "/report/groupmessage/{reportId}/delete",
        summary: "deleteGroupMessageReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deleteGroupMessageReport(int $reportId): void
    {
        $response = JsonBuilder::build();

        try {
            $groupMessageReport = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->delete($reportId);

            $response->addData("id", $groupMessageReport);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}