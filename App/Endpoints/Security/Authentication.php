<?php

namespace Descolar\Endpoints\Security;

use DateTimeImmutable;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Firebase\JWT\JWT;
use OpenAPI\Attributes as OA;

class Authentication extends AbstractEndpoint
{
    #[Get('/authentication', name: 'Authentication', auth: false)]
    #[OA\Get(
        path: "/authentication",
        summary: "Authentication",
        security: [],
        tags: ["Authentication"],
    )]
    #[OA\Response(response: '200', description: 'Token generated successfully')]
    private function getToken(): void
    {
        $secretKey = EnvReader::getInstance()->get('JWT_SECRET');
        $secretKeyEncoded = base64_encode($secretKey ?? '');

        $date       = new DateTimeImmutable();
        $expire_at  = $date->modify('+1 hour')->getTimestamp();
        $domainName = "internal-api.descolar.fr";
        $username   = htmlspecialchars($_GET['username']);

        $request_data = [
            'iat'  => $date->getTimestamp(),        // Issued at: time when the token was generated
            'iss'  => $domainName,                  // Issuer
            'nbf'  => $date->getTimestamp(),        // Not before
            'exp'  => $expire_at,                   // Expire
            'username' => $username,                // User name
        ];

        $jwt = JWT::encode($request_data, $secretKeyEncoded, 'HS256');

        JsonBuilder::build()
            ->setCode(200)
            ->addData('token', $jwt)
            ->addData('token_type', 'Bearer')
            ->getResult();
    }


    #[Post('/verify', name: 'Verify', auth: true)]
    #[OA\Post(
        path: '/verify',
        summary: 'Verify',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    private function verifyJwt(): void
    {
        JsonBuilder::build()
            ->setCode(200)
            ->addData('message', 'Token is valid')
            ->getResult();
    }
}