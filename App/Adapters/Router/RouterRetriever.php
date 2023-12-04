<?php



namespace Descolar\Adapters\Router;

use Descolar\Adapters\Router\Exceptions\EndPointIsNotPrivateException;
use Descolar\Managers\Router\Interfaces\ILink;
use Descolar\Managers\Router\Interfaces\IRoute;
use Descolar\Managers\Router\Interfaces\IRouterManager;
use ReflectionMethod;

class RouterRetriever implements IRouterManager
{
    public function registerRoute(ILink $route, ReflectionMethod $method, array &$routeList): ?IRoute
    {
        return match ($route->getMethod()) {
            'GET', 'POST' => $this->createRoute($route, $method),
            default => null,
        };

    }

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

    private function defineRouteName(ILink $route, ReflectionMethod $method): string
    {
        return $route->getName() ?? "route-" . $method->getName() . uniqid('');
    }
}