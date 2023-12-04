<?php


namespace Descolar\Managers\Router\Interfaces;

use ReflectionMethod;

interface IRouterManager
{

    public function registerRoute(ILink $route, ReflectionMethod $method, array &$routeList): ?IRoute;

}