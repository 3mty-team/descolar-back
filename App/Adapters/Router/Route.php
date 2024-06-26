<?php

namespace Descolar\Adapters\Router;

use Descolar\Adapters\Router\Exceptions\TooManyRequestsException;
use Descolar\Adapters\Security\AuthMiddleware;
use Descolar\Managers\Router\Interfaces\ILink;
use Descolar\Managers\Router\Interfaces\IRoute;
use Override;
use function PHPUnit\Framework\throwException;

/**
 * Route adapted for the router
 */
class Route implements IRoute
{
    /**
     * @var array{string} $matches matched Url parameters
     */
    private array $matches;

    /**
     * @var array<string, string> $params route parameters
     */
    private array $params;

    /**
     * @param string $path The path of the route
     * @param string $name The name of the route
     * @param callable $callable The callable of the route
     * @param ILInk $route The path of the route
     */
    public function __construct(
        private string          $path,
        private readonly string $name,
        private readonly mixed  $callable,
        private readonly ILink  $route,
    )
    {
        $this->path = trim($path, '/');
        $this->matches = [];
        $this->params = [];
    }

    #[Override] public function getPath(): string
    {
        return $this->path;
    }

    #[Override] public function getName(): string
    {
        return $this->name;
    }

    #[Override] public function &getParams(): array
    {
        return $this->params;
    }

    #[Override] public function getUrl(array $params = array()): string
    {
        $path = $this->path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }

    #[Override] public function getRoute(): ILink
    {
        return $this->route;
    }

    /**
     * @inheritDoc
     * @param RouteParam $regex The regex to define the param
     */
    #[Override] public function with(string $param, mixed $regex): self
    {
        $this->getParams()[$param] = str_replace('(', '(?:', $regex->value);
        return $this;
    }

    /**
     * Generate Regex for a parameter
     *
     * @param array<string> $match
     * @return string regex for the parameter
     */
    private function paramMatch(array $match): string
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    /**
     * Permettra de capturer l'url avec les paramètre
     * get('/posts/:slug-:id') par exemple
     * @param $url
     * @return bool
     */
    #[Override] public function match($url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:(\w+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    #[Override] public function call(): void
    {
        if (Control::getInstance()->isOverLimit()) {
            throw new TooManyRequestsException();
        }

        if ($this->route->getModerationAuth($this->route->getAuth())) {
            AuthMiddleware::validateModerationToken();
        }

        if ($this->route->getAuth()) {
            AuthMiddleware::validateJwt();
        }

        call_user_func_array($this->callable, [...$this->matches]);
    }

}