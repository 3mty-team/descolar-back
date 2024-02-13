<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Error\Exceptions\ErrorHandlerNotFoundException;
use Descolar\Managers\Error\Interfaces\IErrorManager;

trait ErrorHandlerAdapter
{
    use BaseAdapter;

    private static ?IErrorManager $_errorHandler = null;

    /**
     * Set the error handler adapter to be used by the application.
     *
     * @param class-string<IErrorManager> $errorManagerClazz the error manager class.
     * @throws ErrorHandlerNotFoundException if the error manager is not found or if he doesn't extend {@see IErrorManager} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useErrorHandler(string $errorManagerClazz): void
    {
        self::useAdapter(self::$_errorHandler, ErrorHandlerNotFoundException::class, IErrorManager::class, $errorManagerClazz);
    }

    /**
     * Return the error manager, if it is set, from adapters.
     *
     * @return IErrorManager|null the error manager.
     * @throws ErrorHandlerNotFoundException if the error manager is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getErrorHandler(): ?IErrorManager
    {
        return self::getAdapter(self::$_errorHandler, ErrorHandlerNotFoundException::class);
    }

}