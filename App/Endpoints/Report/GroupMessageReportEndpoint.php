<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\GroupMessageReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
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
        $this->reply(function ($response) {
            $groupMessageReports = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->findAll();

            $data = [];
            foreach ($groupMessageReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->toJson($report);
            }

            $response->addData('group_message_reports', $data);
        });
    }

    #[Post('/report/groupmessage/create', name: 'createGroupMessageReport', auth: true)]
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
        $this->reply(function ($response) {
            $groupMessageId = $_POST['group_message_id'] ?? 0;
            $reportCategoryId = $_POST['report_category_id'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'];

            $groupMessageReport = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->create($groupMessageId, $reportCategoryId, $comment, $date);
            $groupMessageReportData = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->toJson($groupMessageReport);

            foreach ($groupMessageReportData as $key => $value) {
                $response->addData($key, $value);
            }
        });
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
        $this->reply(function ($response) use ($reportId){
            $groupMessageReport = OrmConnector::getInstance()->getRepository(GroupMessageReport::class)->delete($reportId);

            $response->addData("id", $groupMessageReport);
        });
    }
}