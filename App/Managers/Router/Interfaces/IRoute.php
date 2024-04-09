<?php

namespace Descolar\Managers\Router\Interfaces;

/**
 * Adapter for the route
 */
interface IRoute
{

    /**
     * Get the path of the route
     *
     * @return string The path of the route
     */
    public function getPath(): string;

    /**
     * Get the name of the route
     *
     * @return string|null The name of the route
     */
    public function getName(): ?string;

    /**
     * Get parameters of the route, should be filled when the route is matched
     *
     * @return array<string, string> The params of the route
     */
    public function getParams(): array;

    /**
     * get the url of the route with the edited params
     *
     * @param array<string, string> $params The params of the route
     * @return string The url of the route with the edited params
     */
    public function getUrl(array $params = array()): string;

    /**
     * get the route
     *
     * @return ILink The route
     */
    public function getRoute(): ILink;

    /**
     * add a param to the route
     *
     * @param string $param The param to add
     * @param mixed $regex The regex to define the param
     * @return self The route with the new param
     */
    public function with(string $param, mixed $regex): self;

    /**
     * check if the url match the route
     *
     * @param string $url The url to match
     * @return bool True if the url match the route, false otherwise
     */
    public function match(string $url): bool;

    /**
     * call the callable of the route
     */
    public function call(): void;

}