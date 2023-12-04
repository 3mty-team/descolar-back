<?php

namespace Descolar;

use Descolar\Managers\Annotation\RouterAnnotationManager;
use Descolar\Managers\Router\Exceptions\RouterManagerNotFoundException;
use Descolar\Managers\Router\Interfaces\IRouterManager;
use Descolar\Managers\Router\Router;
use ReflectionClass;
use ReflectionException;

class App
{

    private static ?IRouterManager $_routerManager = null;

    /**
     * @throws ReflectionException
     */
    private static function manageRouter(): void
    {
        (new RouterAnnotationManager())->getClassesWithLinkAttributes(DIR_ROOT . "/App/Endpoints");
    }

    /**
     * @param class-string<IRouterManager> $routerManagerClazz
     */
    public static function useRouter(string $routerManagerClazz): void
    {

        if(!class_exists($routerManagerClazz) || !is_subclass_of($routerManagerClazz, IRouterManager::class)) {
            throw new RouterManagerNotFoundException();
        }

        try {
            $routerManagerReflection = new ReflectionClass($routerManagerClazz);
            self::$_routerManager = $routerManagerReflection->newInstance();
        } catch (ReflectionException $ignored) {
            throw new RouterManagerNotFoundException();
        }

    }

    /**
     * @return IRouterManager|null
     */
    public static function getRouterManager(): ?IRouterManager
    {
        if(is_null(self::$_routerManager)) {
            throw new RouterManagerNotFoundException();
        }

        return self::$_routerManager;
    }

    /**
     * @throws ReflectionException
     */
    public static function run(): void
    {
        self::manageRouter();
        Router::getInstance()->listen();
    }
}