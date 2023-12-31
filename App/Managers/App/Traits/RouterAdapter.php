<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Router\Annotations\Link;
use Descolar\Managers\Router\Exceptions\RouterManagerNotFoundException;
use Descolar\Managers\Router\Interfaces\IRouterManager;
use ReflectionException;

trait RouterAdapter
{
    use BaseAdapter;

    private static ?IRouterManager $_routerManager = null;

    /**
     * Set the router manager adapter to be used by the application.
     *
     * @param class-string<IRouterManager> $routerManagerClazz the router manager class.
     * @throws RouterManagerNotFoundException if the router manager is not found or if he doesn't extend {@see IRouterManager} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useRouter(string $routerManagerClazz): void
    {
        self::useAdapter(self::$_routerManager, RouterManagerNotFoundException::class, IRouterManager::class, $routerManagerClazz);
    }

    /**
     * Return the router manager, if it is set, from adapters.
     *
     * @return IRouterManager|null the router manager.
     * @throws RouterManagerNotFoundException if the router manager is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getRouterManager(): ?IRouterManager
    {
        return self::getAdapter(self::$_routerManager, RouterManagerNotFoundException::class);
    }

    /**
     * Load all Link attributes from the Endpoints directory.
     *
     * @throws ReflectionException
     */
    private static function manageRouter(): void
    {
        $ENDPOINT_DIR = DIR_ROOT . "/App/Endpoints";
        $routerAnnotation = new AnnotationManager(Link::class);
        $routerAnnotation->generateAttributes($ENDPOINT_DIR);
    }

}