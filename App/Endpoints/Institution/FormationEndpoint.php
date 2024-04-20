<?php

namespace Descolar\Endpoints\Institution;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class FormationEndpoint extends AbstractEndpoint
{

    #[Get('/institution/formations', name: 'getAllFormations', auth: true)]
    #[OA\Get(path: "/institution/formations", summary: "getAllFormations", tags: ["Institution"], responses: [new OA\Response(response: 200, description: "All formations retrieved")])]
    private function getAllGroups(): void
    {

        /** @var Formation[] $formations */
        $formations = OrmConnector::getInstance()->getRepository(Formation::class)->findAll();

        $data = [];
        foreach ($formations as $formation) {
            $data[] = OrmConnector::getInstance()->getRepository(Formation::class)->toJson($formation);
        }

        $response = JsonBuilder::build()->setCode(200);
        $response->addData('formations', $data);

        $response->getResult();
    }

    #[Get('/institution/formations/:id', variables: ["id" => RouteParam::NUMBER], name: 'getFormationById', auth: true)]
    #[OA\Get(path: "/institution/formations/{id}", summary: "getFormationById", tags: ["Institution"], parameters: [new PathParameter("id", "id", "Formation ID", required: true)], responses: [new OA\Response(response: 200, description: "Formation retrieved")])]
    private function getGroupById(int $id): void
    {
        $response = JsonBuilder::build();

        try {
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->findById($id);
            $formationData = OrmConnector::getInstance()->getRepository(Formation::class)->toJson($formation);

            foreach ($formationData as $key => $value) {
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