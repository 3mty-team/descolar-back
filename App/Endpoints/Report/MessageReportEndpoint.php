<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\MessageReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class MessageReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/message', name: 'getAllMessageReports', moderationAuth: true)]
    #[OA\Get(
        path: "/report/message",
        summary: "getAllMessageReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All message reports retrieved")])]
    private function getAllMessageReports(): void
    {
        $this->reply(function ($response) {
            $messageReports = OrmConnector::getInstance()->getRepository(MessageReport::class)->findAll();

            $data = [];
            foreach ($messageReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(MessageReport::class)->toJson($report);
            }

            $response->addData('message_reports', $data);
        });
    }

    #[Post('/report/message/create', name: 'createMessageReport', auth: true)]
    #[OA\Post(
        path: "/report/message/create",
        summary: "createMessageReport",
        tags: ["Report"],
        responses: [
            new OA\Response(response: 200, description: "Message report added"),
            new OA\Response(response: 404, description: "Message or User not found")]
    )]
    private function createPostReport(): void
    {
        $this->reply(function ($response) {
            [$messageId, $reportCategoryId, $comment, $date] = Requester::getInstance()->trackMany(
                "message_id", "report_category_id", "comment", "date"
            );

            $messageReport = OrmConnector::getInstance()->getRepository(MessageReport::class)->create($messageId, $reportCategoryId, $comment, $date);
            $messageReportData = OrmConnector::getInstance()->getRepository(MessageReport::class)->toJson($messageReport);

            foreach ($messageReportData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/report/message/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deleteMessageReport', moderationAuth: true)]
    #[OA\Delete(
        path: "/report/message/{reportId}/delete",
        summary: "deleteMessageReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deleteMessageReport(int $reportId): void
    {
        $this->reply(function ($response) use ($reportId){
            $messageReport = OrmConnector::getInstance()->getRepository(MessageReport::class)->delete($reportId);

            $response->addData("id", $messageReport);
        });
    }
}