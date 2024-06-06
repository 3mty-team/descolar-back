<?php

namespace Descolar\Adapters\Requester\Types;

use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\Managers\Requester\Requests\RequestType;

class DeleteRequest extends RequestType
{

    private static ?DeleteRequest $_instance = null;

    public static function getInstance(): RequestType
    {
        if (self::$_instance === null) {
            self::$_instance = new DeleteRequest('DELETE', '_DELETE');
        }
        return self::$_instance;
    }

    public function getItem(string $key): mixed
    {
        global $_REQ;
        RequestUtils::cleanBody();
        return $_REQ[$key] ?? null;
    }
}