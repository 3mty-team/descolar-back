<?php

namespace Descolar\Managers\Router\Annotations;

use Attribute;
use Descolar\Managers\Router\Interfaces\ILink;

/**
 * Base class for the link (Attribute)
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
abstract readonly class Link implements ILink
{

    /**
     * @param string $path The path of the route
     * @param array<string, string> $variables The variables of the route
     * @param string|null $name The name of the route
     * @param bool|null $auth The auth of the route
     */
    public function __construct(
        private string $path,
        private array $variables = array(),
        private ?string $name = null,
        private ?bool $auth = false
    )
    {
    }

    /**
     * @see ILink::getMethod()
     */
    public abstract function getMethod(): string;

    /**
     * @see ILink::getPath()
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @see ILink::getVariables()
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @see ILink::getName()
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @see ILink::getAuth()
     */
    public function getAuth(): ?bool
    {
        return $this->auth;
    }
}