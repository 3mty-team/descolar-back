<?php

namespace Descolar\Managers\Router;

use Descolar\App;
use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Router\Annotations\Link;
use Descolar\Managers\Router\Exceptions\NotFoundException;
use Descolar\Managers\Router\Exceptions\RequestException;
use Descolar\Managers\Router\Exceptions\RouteAlreadyExistsException;
use Descolar\Managers\Router\Interfaces\ILink;
use Descolar\Managers\Router\Interfaces\IRoute;
use ReflectionMethod;

class Router
{
    private static ?IRoute $actualRoute = null;
    private static ?Router $_routerInstance = null;

    /**
     * Simple Singleton to retrieve the Router Instance
     *
     * @return self Router Instance
     */
    public static function getInstance(): self
    {
        if (!isset(self::$_routerInstance)) {
            self::$_routerInstance = new self($_GET['url'] ?? "");
        }

        return self::$_routerInstance;
    }

    /**
     * Get the actual route
     *
     * @return IRoute|null Actual Route
     */
    public static function getActualRoute(): ?IRoute
    {
        return self::$actualRoute;
    }

    /**
     * Set the actual route
     *
     * @param IRoute|null $actualRoute Actual Route
     * @return void
     */
    public static function setActualRoute(?IRoute $actualRoute): void
    {
        self::$actualRoute = $actualRoute;
    }

    /**
     * Main Router constructor
     *
     * @param string $url Actual URL
     * @param array<string, IRoute[]> $routeList List of available route, separated by subarray with <b>REQUEST_METHOD</b>, by default is empty
     */
    public function __construct(
        private readonly string $url,
        private array           $routeList = array()
    )
    {
    }

    /**
     * Add a route to the route list
     *
     * @throws RouteAlreadyExistsException If the route already exists
     * @param ILink $link Link to add (It's an attribute)
     * @param IRoute $route Route to add (It's the link with the callable)
     */
    private function addRoute(ILink $link, IRoute $route): void
    {
        if(!isset($this->routeList[$link->getMethod()])) {
            $this->routeList[$link->getMethod()] = array();
        }

        foreach ($this->routeList[$link->getMethod()] as $routeFromRouter) {
            /** @var IRoute $route */
            if ($routeFromRouter->getPath() === $link->getPath()) {
                throw new RouteAlreadyExistsException($routeFromRouter->getPath());
            }
        }


        $this->routeList[$link->getMethod()][] = $route;
    }

    /**
     * Get the route list
     *
     * @return array<string, IRoute[]> Route list
     */
    public function &getRoutes(): array
    {
        return $this->routeList;
    }

    /**
     * Get the route by the URL, null otherwise
     *
     * @param string $url URL to match
     * @return IRoute|null Route matched, null if not
     */
    public function getRouteByUrl(string $url): ?IRoute
    {
        $matchedRoute = null;
        foreach ($this->getRoutes()[$_SERVER['REQUEST_METHOD']] as $route) {

            if ($route->match($url)) {
                $matchedRoute = $route;
            }
        }

        return $matchedRoute;
    }

    /**
     * Register a route to the route list
     *
     * @param ILink $link Link to add (It's an attribute)
     * @param ReflectionMethod $method Method to add
     */
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

    /**
     * Load the routes and register them
     */
    private function loadRoutes(): void
    {
        $routes = (new AnnotationManager(Link::class))->getAttributeList();
        /**
         * @var ILink $link
         * @var ReflectionMethod $method
         */
        foreach ($routes as [$link, $method]) {
            $this->registerRoute($link, $method);
        }
    }

    /**
     * Listen to the request and call the route
     *
     * @throws NotFoundException If the route is not found
     * @throws RequestException If the request method is not supported
     */
    public function listen(): void
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

        $matchedRoute->call();
    }


}