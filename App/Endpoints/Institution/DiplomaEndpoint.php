<?php

namespace Descolar\Endpoints\Institution;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Institution\Diploma;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class DiplomaEndpoint extends AbstractEndpoint
{

    #[Get('/institution/diplomas', name: 'getAllDiplomas', auth: true)]
    #[OA\Get(path: "/institution/diplomas", summary: "getAllDiplomas", tags: ["Institution"], responses: [new OA\Response(response: 200, description: "All diplomas retrieved")])]
    private function getAllDiplomas(): void
    {

        /** @var Diploma[] $diplomas */
        $diplomas = OrmConnector::getInstance()->getRepository(Diploma::class)->findAll();

        $data = [];
        foreach ($diplomas as $diploma) {
            $data[] = OrmConnector::getInstance()->getRepository(Diploma::class)->toJson($diploma);
        }

        $response = JsonBuilder::build()->setCode(200);
        $response->addData('diplomas', $data);

        $response->getResult();
    }

    #[Get('/institution/diplomas/:id', variables: ["id" => RouteParam::NUMBER], name: 'getDiplomaById', auth: true)]
    #[OA\Get(path: "/institution/diplomas/{id}", summary: "getDiplomaById", tags: ["Institution"], parameters: [new PathParameter("id", "id", "Diploma ID", required: true)], responses: [new OA\Response(response: 200, description: "Diploma retrieved")])]
    private function getDiplomaById(int $id): void
    {
        $response = JsonBuilder::build();

        try {
            $diploma = OrmConnector::getInstance()->getRepository(Diploma::class)->findById($id);
            $diplomaData = OrmConnector::getInstance()->getRepository(Diploma::class)->toJson($diploma);

            foreach ($diplomaData as $key => $value) {
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
}