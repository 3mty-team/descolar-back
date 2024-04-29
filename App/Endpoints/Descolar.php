<?php

namespace Descolar\Endpoints;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use OpenApi\Generator;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Api interne officielle de Descolar",
    title: "Private Descolar API",
)]
#[OA\Contact(
    name: "Descolar Team",
    url: "https://descolar.fr",
    email: "contact@descolar.fr",
)]
#[OA\OpenApi(
    servers: [new OA\Server(url: "https://internal-api.descolar.fr/v1")],
    security: [['JWT' => []]],
    tags: [
        new OA\Tag(name: "Group", description: "Endpoints for group"),
        new OA\Tag(name: "Authentication", description: "Endpoints for authentication"),
        new OA\Tag(name: "Configuration", description: "Endpoints for configuration"),
    ]
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
class Descolar extends AbstractEndpoint
{

    /**
     * Get OpenAPI Json file
     */
    #[OA\Get(path: '/api/data.json', summary: 'Get OpenAPI Json file', security: [], responses: [new OA\Response(response: 200, description: 'Data JSON File')])]
    #[OA\Post(path: '/api/data.json', summary: 'Get OpenAPI Json file', security: [], responses: [new OA\Response(response: 200, description: 'Data JSON File')], deprecated: true)]
    #[OA\Put(path: '/api/data.json', summary: 'Get OpenAPI Json file', security: [], responses: [new OA\Response(response: 200, description: 'Data JSON File')], deprecated: true)]
    #[OA\Delete(path: '/api/data.json', summary: 'Get OpenAPI Json file', security: [], responses: [new OA\Response(response: 200, description: 'Data JSON File')], deprecated: true)]
    #[Get('/api/data.json', name: 'retrieve-swagger-data', auth: false)]
    private function getResource(): void
    {
        $root = DIR_ROOT . '/App/Endpoints';
        $openapi = Generator::scan([array_filter(glob($root . '/*'), 'is_dir'), $root]);
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }
}