<?php



namespace Descolar\Adapters\Router;

use Descolar\Managers\Router\Interfaces\IRoute;

class Route implements IRoute
{

    private array $matches;
    private array $params;

    /**
     * @param string $path
     * @param string $name
     * @param callable $callable
     * @param array $matches
     * @param array $params
     */
    public function __construct(
        private string $path,
        private string $name,
        private mixed  $callable,
    )
    {
        $this->path = trim($path, '/');
        $this->matches = [];
        $this->params = [];
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function &getParams(): array
    {
        return $this->params;
    }

    public function getUrl(array $params = array()): string
    {
        $path = $this->path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }

    /**
     * Fonction qui permet de capturer les paramètres
     * @param $param
     * @param $regex
     * @return $this
     */
    public function with($param, $regex): self
    {
        $this->getParams()[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    private function paramMatch($match): string
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
    public function match($url): bool
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

    public function call(): mixed
    {
        return call_user_func_array($this->callable, $this->matches);
    }

}