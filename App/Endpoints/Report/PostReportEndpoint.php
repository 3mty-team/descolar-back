<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Report\PostReport;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class PostReportEndpoint extends AbstractEndpoint
{
    #[Get('/report/post', name: 'getAllPostReports', auth: false)]
    #[OA\Get(
        path: "/report/post",
        summary: "getAllPostReports",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All post reports retrieved")])]
    private function getAllPostReports(): void
    {
        $this->reply(function ($response){
            $postReports = OrmConnector::getInstance()->getRepository(PostReport::class)->findAll();

            $data = [];
            foreach ($postReports as $report) {
                $data[] = OrmConnector::getInstance()->getRepository(PostReport::class)->toJson($report);
            }

            $response->addData('post_reports', $data);
        });
    }

    #[Post('/report/post/create', name: 'createPostReport', auth: true)]
    #[OA\Post(
        path: "/report/post/create",
        summary: "createPostReport",
        tags: ["Report"],
        responses: [
            new OA\Response(response: 200, description: "Post report added"),
            new OA\Response(response: 404, description: "Post or User not found")]
    )]
    private function createPostReport(): void
    {
        $this->reply(function ($response){
            $postId = $_POST['post_id'] ?? 0;
            $reportCategoryId = $_POST['report_category_id'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'];

            $postReport = OrmConnector::getInstance()->getRepository(PostReport::class)->create($postId, $reportCategoryId, $comment, $date);
            $postReportData = OrmConnector::getInstance()->getRepository(PostReport::class)->toJson($postReport);

            foreach ($postReportData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }

    #[Delete('/report/post/:reportId/delete', variables: ["reportId" => RouteParam::NUMBER], name: 'deletePostReport', auth: false)]
    #[OA\Delete(
        path: "/report/post/{reportId}/delete",
        summary: "deletePostReport",
        tags: ["Report"],
        parameters: [new PathParameter("reportId", "reportId", "Report ID", required: true)],
        responses: [new OA\Response(response: 200, description: "Report deleted")])]
    private function deletePostReport(int $reportId): void
    {
        $this->reply(function ($response) use ($reportId){
            $postReport = OrmConnector::getInstance()->getRepository(PostReport::class)->delete($reportId);

            $response->addData("id", $postReport);
        });
    }
}