<?php


namespace Descolar\Managers\Endpoint;

use Descolar\Managers\Endpoint\Interfaces\IEndpoint;

abstract class AbstractEndpoint implements IEndpoint
{
    protected static array $_instances = [];

    public static function getInstance(): static
    {
        $class = static::class;

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new static();
        }

        return self::$_instances[$class];
    }

}