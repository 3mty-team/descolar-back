<?php

namespace Descolar\Endpoints\Institution;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Institution\Diploma;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class DiplomaEndpoint extends AbstractEndpoint
{

    #[Get('/institution/diplomas', name: 'getAllDiplomas', auth: true)]
    #[OA\Get(path: "/institution/diplomas", summary: "getAllDiplomas", tags: ["Institution"], responses: [new OA\Response(response: 200, description: "All diplomas retrieved")])]
    private function getAllDiplomas(): void
    {
        $this->reply(function ($response){
            /** @var Diploma[] $diplomas */
            $diplomas = OrmConnector::getInstance()->getRepository(Diploma::class)->findAll();

            $data = [];
            foreach ($diplomas as $diploma) {
                $data[] = OrmConnector::getInstance()->getRepository(Diploma::class)->toJson($diploma);
            }

            $response->addData('diplomas', $data);
        });
    }

    #[Get('/institution/diplomas/:id', variables: ["id" => RouteParam::NUMBER], name: 'getDiplomaById', auth: true)]
    #[OA\Get(path: "/institution/diplomas/{id}", summary: "getDiplomaById", tags: ["Institution"], parameters: [new PathParameter("id", "id", "Diploma ID", required: true)], responses: [new OA\Response(response: 200, description: "Diploma retrieved")])]
    private function getDiplomaById(int $id): void
    {
        $this->reply(function ($response) use ($id){
            $diploma = OrmConnector::getInstance()->getRepository(Diploma::class)->findById($id);
            $diplomaData = OrmConnector::getInstance()->getRepository(Diploma::class)->toJson($diploma);

            foreach ($diplomaData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}