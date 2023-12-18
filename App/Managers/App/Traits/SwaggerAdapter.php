<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Swagger\Exceptions\SwaggerNotFoundException;
use Descolar\Managers\Swagger\Interfaces\ISwagger;
use ReflectionClass;
use ReflectionException;

trait SwaggerAdapter
{
    use BaseAdapter;

    private static ?ISwagger $_swagger = null;

    /**
     * Set the swagger adapter to be used by the application.
     *
     * @param class-string<ISwagger> $swaggerClazz the swagger class.
     * @throws SwaggerNotFoundException if swagger is not found or if he doesn't extend {@see ISwagger} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useSwagger(string $swaggerClazz): void
    {
        self::useAdapter(self::$_swagger, SwaggerNotFoundException::class, ISwagger::class, $swaggerClazz);
    }

    /**
     * Return swagger, if it is set, from adapters.
     *
     * @return ISwagger|null swagger.
     * @throws SwaggerNotFoundException if swagger is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getSwaggerAdapter(): ?ISwagger
    {
        return self::getAdapter(self::$_swagger, SwaggerNotFoundException::class);
    }

}