<?php

namespace Descolar\Endpoints\Report;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Data\Entities\Report\ReportCategory;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
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
        $response = JsonBuilder::build();

        try {
            $reportCategories = OrmConnector::getInstance()->getRepository(ReportCategory::class)->findAll();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

        $data = [];
        foreach ($reportCategories as $category) {
            $data[] = $category->getName();
        }

        $response = JsonBuilder::build()->setCode(200);
        $response->addData('report_categories', $data);
        $response->getResult();
    }
}