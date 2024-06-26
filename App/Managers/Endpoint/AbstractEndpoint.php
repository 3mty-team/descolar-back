<?php

namespace Descolar\Managers\Endpoint;

use Closure;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Endpoint\Interfaces\IEndpoint;
use Descolar\Managers\JsonBuilder\Interfaces\IJsonBuilder;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Validator\Exceptions\PropertyIsNotValidException;
use Override;

/**
 * Base class for endpoints
 */
abstract class AbstractEndpoint implements IEndpoint
{

    /**
     * @var array<string, AbstractEndpoint> The instances of the endpoints can be any AbstractEndpoint child.
     */
    protected static array $_instances = [];

    #[Override] public final static function getInstance(): static
    {
        $class = static::class;

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new static();
        }

        return self::$_instances[$class];
    }

    /**
     * Execute the method in the endpoint and return the response with try/catch
     * @param Closure $closure The method to execute in the endpoint
     */
    protected final function reply(Closure $closure): void
    {
        $response = $this->getResponse();

        try {
            $closure($response);
            $response->setCode(200);
        } catch (EndpointException | PropertyIsNotValidException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
        } finally {
            $response->getResult();
        }

    }

    /**
     * @return IJsonBuilder The json builder
     */
    protected final function getResponse(): IJsonBuilder
    {
        return JsonBuilder::build();
    }

}