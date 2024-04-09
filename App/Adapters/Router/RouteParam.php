<?php

namespace Descolar\Adapters\Router;

enum RouteParam : string
{
    case UUID = '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}';
    case NUMBER = '[0-9]+';
    case STRING = '.*';
    case BOOLEAN = 'true|false';
    case TIMESTAMP = '[0-9]{10}';

}
