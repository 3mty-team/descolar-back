<?php

namespace Descolar\Adapters\Security;

use Descolar\Adapters\Security\Exceptions\UnauthorizedException;
use Descolar\App;
use Descolar\Managers\Env\EnvReader;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function validateJwt(): void
    {
        $secretKey = EnvReader::getInstance()->get('JWT_SECRET');
        $secretKeyEncoded = base64_encode($secretKey ?? '');
        try {
            $jwt = $_SERVER['HTTP_AUTHORIZATION'];
            $jwt = str_replace('Bearer ', '', $jwt);
            $hashedUsername = JWT::decode($jwt, new Key($secretKeyEncoded, 'HS256'))->username;
            $userUuid = hash('sha256', $hashedUsername);
            App::setUserUuid($userUuid);
        } catch (Exception $e) {
            throw new UnauthorizedException();
        }
    }
}