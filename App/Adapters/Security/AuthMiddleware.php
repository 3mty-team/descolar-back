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
            $userUUID = JWT::decode($jwt, new Key($secretKeyEncoded, 'HS256'))->userUUID;

            App::setUserUuid($userUUID);
        } catch (Exception $e) {
            throw new UnauthorizedException();
        }
    }

    public static function validateModerationToken(bool $isJWTAuth): void
    {
        $envToken = EnvReader::getInstance("/app/descolar-env/")->get('TOKEN');

        try {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
            $token = str_replace('Bearer ', '', $token);
        } catch (Exception $e) {
            if (!$isJWTAuth) {
                throw new UnauthorizedException();
            }
        }

        if ($token !== $envToken && !$isJWTAuth) {
            throw new UnauthorizedException();
        }
    }
}