<?php

namespace Descolar\Managers\Router\Interfaces;

use ReflectionMethod;

/**
 * Adapter for the router manager
 */
interface IRouterManager
{

    /**
     * Register a route
     *
     * @param ILink $route The attribute that contains the route
     * @param ReflectionMethod $method The method should be called when the route is matched
     * @param array &$routeList reference to the route list
     * @return IRoute|null The route created, null if the route is not valid
     */
    public function registerRoute(ILink $route, ReflectionMethod $method, array &$routeList): ?IRoute;

}