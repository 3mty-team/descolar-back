<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\App;
use OpenApi\Attributes as OA;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use OpenApi\Generator;

#[OA\Info(
    version: "1.0.0",
    description: "Api interne officielle de Descolar",
    title: "Private Descolar API",
)]
#[OA\Contact(
    name: "Descolar Team",
    url: "https://descolar.com",
    email: "contact@descolar.com"
)]
#[OA\Tag(
    name: "Example",
    description: "Example endpoint"
)]
class DescolarAPI extends AbstractEndpoint
{

    /**
     * Get OpenAPI Json file
     */
    #[OA\Get(path: '/api/data.json')]
    #[OA\Response(response: '200', description: 'Data JSON File')]
    #[Get('/api/data.json', name: 'retrieve-swagger-data')]
    private function getResource(): void
    {
        $openapi = Generator::scan([DIR_ROOT . '/App/Endpoints']);
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }

    /**
     * Route for swagger ui
     */
    #[Get(path: '/api', name: 'retrieve-swagger')]
    private function getSwagger(): void
    {
        //TODO View class
        echo App::getSwaggerAdapter()->getContent();
    }
}