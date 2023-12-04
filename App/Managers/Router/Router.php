<?php


namespace Descolar\Managers\Router;

use Descolar\Adapters\Router\Route;
use Descolar\App;
use Descolar\Managers\Annotation\RouterAnnotationManager;
use Descolar\Managers\Router\Exceptions\NotFoundException;
use Descolar\Managers\Router\Exceptions\RequestException;
use Descolar\Managers\Router\Exceptions\RouteAlreadyExistsException;
use Descolar\Managers\Router\Interfaces\ILink;
use Descolar\Managers\Router\Interfaces\IRoute;
use ReflectionMethod;

class Router
{
    private static ?Route $actualRoute = null;
    private static ?Router $_routerInstance = null;

    public static function getInstance(): Router
    {
        if (!isset(self::$_routerInstance)) {
            self::$_routerInstance = new self($_GET['url'] ?? "");
        }

        return self::$_routerInstance;
    }

    public static function getActualRoute(): ?Route
    {
        return self::$actualRoute;
    }

    public static function setActualRoute(?Route $actualRoute): void
    {
        self::$actualRoute = $actualRoute;
    }

    public function __construct(
        private readonly string $url,
        private array           $routeList = array()
    )
    {
    }

    private function addRoute(ILink $link, IRoute $route): void
    {
        if(!isset($this->routeList[$link->getMethod()])) {
            $this->routeList[$link->getMethod()] = array();
        }

        foreach ($this->routeList[$link->getMethod()] as $routeFromRouter) {
            /** @var Route $route */
            if ($routeFromRouter->getPath() === $link->getPath()) {
                throw new RouteAlreadyExistsException($routeFromRouter->getPath());
            }
        }


        $this->routeList[$link->getMethod()][] = $route;
    }

    public function &getRoutes(): array
    {
        return $this->routeList;
    }

    public function getRouteByUrl(string $url): ?IRoute
    {
        $matchedRoute = null;
        foreach ($this->getRoutes()[$_SERVER['REQUEST_METHOD']] as $route) {


            /** @var Route $route */
            if ($route->match($url)) {
                $matchedRoute = $route;
            }
        }

        return $matchedRoute;
    }

    public function registerRoute(ILink $link, ReflectionMethod $method): void
    {
        $route = App::getRouterManager()->registerRoute($link, $method, $this->getRoutes());

        if (is_null($route)) {
            throw new RequestException($link->getMethod());
        }

        $this->addRoute($link, $route);

        foreach ($link->getVariables() as $value => $regex) {
            $route->with($value, $regex);
        }

    }

    private function loadRoutes(): void
    {
        $routes = RouterAnnotationManager::getAttributeList();

        foreach ($routes as [$link, $method]) {

            /**
             * @var ILink $link
             * @var ReflectionMethod $method
             */
            $this->registerRoute($link, $method);
        }
    }

    public function listen()
    {
        $this->loadRoutes();

        if (!isset($this->getRoutes()[$_SERVER['REQUEST_METHOD']])) {
            throw new RequestException($_SERVER['REQUEST_METHOD']);
        }

        $matchedRoute = $this->getRouteByUrl($this->url);

        if (is_null($matchedRoute)) {
            throw new NotFoundException();
        }

        self::setActualRoute($matchedRoute);
        return $matchedRoute->call();

    }


}