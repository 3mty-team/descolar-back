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
#[OA\OpenApi(
    security: [['JWT' => []]]
)]
#[OA\Components(
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: 'JWT',
            type: 'http',
            name: 'JWT',
            in: 'header',
            scheme: 'Bearer'
        )
    ]
)]
class DescolarAPI extends AbstractEndpoint
{

    /**
     * Get OpenAPI Json file
     */
    #[OA\Get(path: '/api/data.json', security: [])]
    #[OA\Response(response: '200', description: 'Data JSON File')]
    #[Get('/api/data.json', name: 'retrieve-swagger-data', auth: false)]
    private function getResource(): void
    {
        $openapi = Generator::scan([DIR_ROOT . '/App/Endpoints']);
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }

    /**
     * Route for swagger ui
     */
    #[Get(path: '/api', name: 'retrieve-swagger', auth: false)]
    private function getSwagger(): void
    {
        //TODO View class
        echo App::getSwaggerAdapter()->getContent();
    }
}