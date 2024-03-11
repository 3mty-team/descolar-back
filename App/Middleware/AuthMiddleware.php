<?php

namespace Descolar\Middleware;
use Descolar\Managers\Env\EnvReader;
use Descolar\Middleware\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function validateJwt(): void
    {
        $secret_Key = EnvReader::getInstance()->get('JWT_SECRET');
        $secret_Key_encoded = base64_encode($secret_Key);

        try {
            $jwt = $_SERVER['HTTP_AUTHORIZATION'];
            $jwt = str_replace('Bearer ', '', $jwt);
            $decoded = JWT::decode($jwt, new Key($secret_Key_encoded, 'HS256'));
        } catch (\Exception $e) {
            throw new UnauthorizedException();
        }
    }
}