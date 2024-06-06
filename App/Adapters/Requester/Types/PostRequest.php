<?php

namespace Descolar\Adapters\Requester\Types;

use Descolar\Managers\Requester\Requests\RequestType;

class PostRequest extends RequestType
{

    private static ?PostRequest $_instance = null;

    public static function getInstance(): RequestType
    {
        if (self::$_instance === null) {
            self::$_instance = new PostRequest('POST', '_POST');
        }
        return self::$_instance;
    }

    public function getItem(string $key): mixed
    {
        return $_POST[$key] ?? null;
    }
}