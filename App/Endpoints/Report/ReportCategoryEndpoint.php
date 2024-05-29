<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class ReportCategoryEndpoint extends AbstractEndpoint
{
    #[Get('/report/category', name: 'getAllReportCategories', auth: false)]
    #[OA\Get(
        path: "/report/category",
        summary: "getAllReportCategories",
        tags: ["Report"],
        responses: [new OA\Response(response: 200, description: "All report categories retrieved")])]
    private function getAllReportCategories(): void
    {
        $this->reply(function ($response) {
            $reportCategories = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findAll();

            $data = [];
            foreach ($reportCategories as $category) {
                $data[] = $category->getName();
            }

            $response->addData('report_categories', $data);
        });
    }
}