<?php

namespace Descolar\Managers\Endpoint;

use Descolar\Managers\Endpoint\Interfaces\IEndpoint;

/**
 * Base class for endpoints
 */
abstract class AbstractEndpoint implements IEndpoint
{

    /**
     * @var array<string, AbstractEndpoint> The instances of the endpoints, can be any AbstractEndpoint child.
     */
    protected static array $_instances = [];

    /**
     * @see IEndpoint::getInstance()
     */
    public final static function getInstance(): static
    {
        $class = static::class;

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new static();
        }

        return self::$_instances[$class];
    }

}