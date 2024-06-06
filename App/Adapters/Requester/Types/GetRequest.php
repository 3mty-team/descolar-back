<?php

namespace Descolar\Adapters\Requester\Types;

use Descolar\Managers\Requester\Requests\RequestType;

class GetRequest extends RequestType
{

    private static ?GetRequest $_instance = null;

    public static function getInstance(): RequestType
    {
        if (self::$_instance === null) {
            self::$_instance = new GetRequest('GET', '_GET');
        }
        return self::$_instance;
    }

    public function getItem(string $key): mixed
    {
        return $_GET[$key] ?? null;
    }

}