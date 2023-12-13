<?php

namespace Descolar;

use Descolar\Managers\Annotation\RouterAnnotationManager;
use Descolar\Managers\Router\Exceptions\RouterManagerNotFoundException;
use Descolar\Managers\Router\Interfaces\IRouterManager;
use Descolar\Managers\Router\Router;
use ReflectionClass;
use ReflectionException;

/**
 * Main class of Descolar.
 * Should load all the necessary adapters and run the application.
 */
class App
{

    private static ?IRouterManager $_routerManager = null;

    /**
     * Load all Link attributes from the Endpoints directory.
     *
     * @throws ReflectionException
     */
    private static function manageRouter(): void
    {
        $ENDPOINT_DIR = DIR_ROOT . "/App/Endpoints";
        (new RouterAnnotationManager())->getClassesWithLinkAttributes($ENDPOINT_DIR);
    }

    /**
     * Set the router manager adapter to be used by the application.
     *
     * @param class-string<IRouterManager> $routerManagerClazz
     * @throws RouterManagerNotFoundException if the router manager is not found or if he doesn't extend {@see IRouterManager} interface.
     */
    public static function useRouter(string $routerManagerClazz): void
    {

        if (!class_exists($routerManagerClazz) || !is_subclass_of($routerManagerClazz, IRouterManager::class)) {
            throw new RouterManagerNotFoundException();
        }

        try {
            $routerManagerReflection = new ReflectionClass($routerManagerClazz);
            self::$_routerManager = $routerManagerReflection->newInstance();
        } catch (ReflectionException) {
            throw new RouterManagerNotFoundException();
        }

    }

    /**
     * Return the router manager, if it is set, from adapters.
     *
     * @throws RouterManagerNotFoundException if the router manager is not set.
     * @return IRouterManager|null the router manager.
     */
    public static function getRouterManager(): ?IRouterManager
    {
        if (is_null(self::$_routerManager)) {
            throw new RouterManagerNotFoundException();
        }

        return self::$_routerManager;
    }

    /**
     * Start the application and run the router.
     *
     * @throws ReflectionException
     */
    public static function run(): void
    {
        self::manageRouter();
        Router::getInstance()->listen();
    }
}