<?php

namespace Descolar\Endpoints\Institution;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\PathParameter;

class FormationEndpoint extends AbstractEndpoint
{

    #[Get('/institution/formations', name: 'getAllFormations', auth: true)]
    #[OA\Get(path: "/institution/formations", summary: "getAllFormations", tags: ["Institution"], responses: [new OA\Response(response: 200, description: "All formations retrieved")])]
    private function getAllGroups(): void
    {
        $this->reply(function ($response){
            /** @var Formation[] $formations */
            $formations = OrmConnector::getInstance()->getRepository(Formation::class)->findAll();

            $data = [];
            foreach ($formations as $formation) {
                $data[] = OrmConnector::getInstance()->getRepository(Formation::class)->toJson($formation);
            }

            $response->addData('formations', $data);
        });
    }

    #[Get('/institution/formations/:id', variables: ["id" => RouteParam::NUMBER], name: 'getFormationById', auth: true)]
    #[OA\Get(path: "/institution/formations/{id}", summary: "getFormationById", tags: ["Institution"], parameters: [new PathParameter("id", "id", "Formation ID", required: true)], responses: [new OA\Response(response: 200, description: "Formation retrieved")])]
    private function getGroupById(int $id): void
    {
        $this->reply(function ($response) use ($id){
            $formation = OrmConnector::getInstance()->getRepository(Formation::class)->findById($id);
            $formationData = OrmConnector::getInstance()->getRepository(Formation::class)->toJson($formation);

            foreach ($formationData as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}