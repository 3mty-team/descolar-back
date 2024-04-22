<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Media\Exceptions\MediaConnectorNotFound;
use Descolar\Managers\Media\Interfaces\IMediaManager;

trait MediaAdapter
{
    use BaseAdapter;

    private static ?IMediaManager $_media = null;

    /**
     * Set the MediaManager adapter to be used by the application.
     *
     * @param class-string<IMediaManager> $mediaClazz the MediaManager class.
     * @throws MediaConnectorNotFound if the MediaManager is not found, or if he doesn't extend {@see IMediaManager} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useMedia(string $mediaClazz): void
    {
        self::useAdapter(self::$_media, MediaConnectorNotFound::class, IMediaManager::class, $mediaClazz);
    }

    /**
     * Return the MediaManager if it is set, from adapters.
     *
     * @return IMediaManager|null the MediaManager.
     * @throws MediaConnectorNotFound if the MediaManager is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getMediaManager(): ?IMediaManager
    {
        return self::getAdapter(self::$_media, MediaConnectorNotFound::class);
    }

}