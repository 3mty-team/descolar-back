<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\JsonBuilder\Exceptions\JsonBuilderNotFoundException;
use Descolar\Managers\JsonBuilder\Interfaces\IJsonBuilder;

trait JsonBuilderAdapter
{
    use BaseAdapter;

    private static ?IJsonBuilder $_jsonBuilder = null;

    /**
     * Set the jsonBuilder adapter to be used by the application.
     *
     * @param class-string<IJsonBuilder> $jsonBuilderClazz the jsonBuilder class.
     * @throws JsonBuilderNotFoundException if jsonBuilder is not found or if he doesn't extend {@see IJsonBuilder} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useJsonBuilder(string $jsonBuilderClazz): void
    {
        self::useAdapter(self::$_jsonBuilder, JsonBuilderNotFoundException::class, IJsonBuilder::class, $jsonBuilderClazz);
    }

    /**
     * Return jsonBuilder, if it is set, from adapters.
     *
     * @return IJsonBuilder|null jsonBuilder.
     * @throws JsonBuilderNotFoundException if jsonBuilder is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getJsonBuilder(): ?IJsonBuilder
    {
        return self::getAdapter(self::$_jsonBuilder, JsonBuilderNotFoundException::class);
    }

}