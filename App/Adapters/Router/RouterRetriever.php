<?php

namespace Descolar\Adapters\Router;

use Descolar\Adapters\Router\Exceptions\EndPointIsNotPrivateException;
use Descolar\Managers\Router\Interfaces\ILink;
use Descolar\Managers\Router\Interfaces\IRoute;
use Descolar\Managers\Router\Interfaces\IRouterManager;
use ReflectionMethod;

/**
 * [ADAPTER] Class responsible for retrieving routes
 */
class RouterRetriever implements IRouterManager
{
    /**
     * @see IRouterManager::registerRoute()
     */
    public function registerRoute(ILink $route, ReflectionMethod $method, array &$routeList): ?IRoute
    {
        return match ($route->getMethod()) {
            'GET', 'POST' => $this->createRoute($route, $method),
            default => null,
        };

    }

    /**
     * Create {@see Route}, to be used in {@see Router}
     *
     * @param ILink $route The route to be created
     * @param ReflectionMethod $method The method of the route
     * @return Route The route created
     */
    private function createRoute(ILink $route, ReflectionMethod $method): IRoute {
        return new Route(
            $route->getPath(),
            $this->defineRouteName($route, $method),
            function (...$values) use ($method) {

                $classInstance = $method->getDeclaringClass()->getMethod("getInstance")->invoke(null);
                if(!$method->isPrivate()) {
                    throw new EndPointIsNotPrivateException($method);
                }
                $method->invoke($classInstance, ...$values);

            }
        );
    }

    /**
     * Define the name of the route if it is not defined
     *
     * @param ILink $route The route to be named
     * @param ReflectionMethod $method The method of the route
     * @return string The main name of the route if it defined, otherwise a random name <code>(route-{method name}-{random id})</code>
     */
    private function defineRouteName(ILink $route, ReflectionMethod $method): string
    {
        return $route->getName() ?? "route-" . $method->getName() . uniqid();
    }
}