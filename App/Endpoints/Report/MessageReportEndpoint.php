<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\MessageReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class MessageReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/message', name: 'getAllMessageReports', auth: false)]
    #[OA\Get(
        path: "/report/message",
        summary: "getAllMessageReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All message reports retrieved")])]
    private function getAllMessageReports(): void
    {
        $response = JsonBuilder::build();

        try {
            $messageReports = OrmConnector::getInstance()->getRepository(MessageReport::class)->findAll();

            $data = [];
            foreach ($messageReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(MessageReport::class)->toJson($report);
            }

            $response->setCode(200);
            $response->addData('message_reports', $data);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Post('/report/message/create', name: 'createMessageReport', auth: false)]
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
        $response = JsonBuilder::build();

        try {
            $messageId = $_POST['message_id'] ?? 0;
            $reportCategoryId = $_POST['report_category_id'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'];

            $messageReport = OrmConnector::getInstance()->getRepository(MessageReport::class)->create($messageId, $reportCategoryId, $comment, $date);
            $messageReportData = OrmConnector::getInstance()->getRepository(MessageReport::class)->toJson($messageReport);

            foreach ($messageReportData as $key => $value) {
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

    #[Delete('/report/message/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deleteMessageReport', auth: false)]
    #[OA\Delete(
        path: "/report/message/{reportId}/delete",
        summary: "deleteMessageReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deleteMessageReport(int $reportId): void
    {
        $response = JsonBuilder::build();

        try {
            $messageReport = OrmConnector::getInstance()->getRepository(MessageReport::class)->delete($reportId);

            $response->addData("id", $messageReport);
            $response->setCode(200);
            $response->getResult();

        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }
}