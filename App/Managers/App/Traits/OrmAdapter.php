<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Orm\Exceptions\OrmConnectorNotFound;
use Descolar\Managers\Orm\Interfaces\IOrmManager;

trait OrmAdapter
{
    use BaseAdapter;

    private static ?IOrmManager $_orm = null;

    /**
     * Set the OrmManager adapter to be used by the application.
     *
     * @param class-string<IOrmManager> $ormClazz the OrmManager class.
     * @throws OrmConnectorNotFound if the OrmManager is not found or if he doesn't extend {@see IOrmManager} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useOrm(string $ormClazz): void
    {
        self::useAdapter(self::$_orm, OrmConnectorNotFound::class, IOrmManager::class, $ormClazz);
    }

    /**
     * Return the OrmManager, if it is set, from adapters.
     *
     * @return IOrmManager|null the OrmManager.
     * @throws OrmConnectorNotFound if the OrmManager is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getOrmManager(): ?IOrmManager
    {
        return self::getAdapter(self::$_orm, OrmConnectorNotFound::class);
    }

}