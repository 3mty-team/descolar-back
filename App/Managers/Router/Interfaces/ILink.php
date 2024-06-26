<?php

namespace Descolar\Managers\Router\Interfaces;

/**
 * Adapter for the Link (Attribute)
 */
interface ILink
{

    /**
     * Get the method of the route, values can be all values retrieved by $_SERVER['REQUEST_METHOD']
     *
     * @return string The method of the route
     */
    public function getMethod(): string;

    /**
     * Get the path of the route
     *
     * @return string The path of the route
     */
    public function getPath(): string;

    /**
     * Get the variables of the route, same functionality as "params"
     *
     * @return array<string, string> The variables of the route
     */
    public function getVariables(): array;

    /**
     * Get the name of the route
     *
     * @return string|null The name of the route
     */
    public function getName(): ?string;

    /**
     * Get the auth of the route
     *
     * @return bool|null The auth of the route
     */
    public function getAuth(): ?bool;

}